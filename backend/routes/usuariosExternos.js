const express = require('express');
const router = express.Router();
const soap = require('soap');

const WSDL_URL = 'http://10.8.17.131:8010/PSIGW/PeopleSoftServiceListeningConnector/PSFT_HR/PH_ALTA_COLABORADORES.1.wsdl';

router.get('/:id', async (req, res) => {
  const colaboradorID = req.params.id;

  try {
    const client = await soap.createClientAsync(WSDL_URL);

    // WS-Security (tipo texto)
    const wsSecurity = new soap.WSSecurity('usuarioaltas', 'usuarioaltasws', {
      passwordType: 'PasswordText',
      hasTimeStamp: false
    });
    client.setSecurity(wsSecurity);

    const args = {
      FieldTypes: {
        PH_COLAB_SAP_TB: { EMPLID: colaboradorID, class: 'R' },
        PSCAMA: { class: 'R' }
      },
      MsgData: {
        Transaction: {
          PH_COLAB_SAP_TB: { EMPLID: colaboradorID, class: 'R' },
          PSCAMA: { class: 'R' }
        }
      }
    };

    const [result] = await client.AltaColab.PH_ALTA_COLABORADORES_Port.AltaColaboradoresAsync({ PH_ALTA_COLAB_REQ: args });

    const datos = result?.PH_ALTA_COLAB_RESP?.MsgData?.Transaction?.PH_COLAB_ACT_TB;
    if (!datos) return res.status(404).json({ error: 'No se encontró colaborador' });

    const nombre = datos.FIRST_NAME || '';
    const apellido = datos.LAST_NAME100 || '';
    res.json({ nombreCompleto: `${nombre} ${apellido}` });

  } catch (error) {
    console.error('❌ Error SOAP:', error);
    res.status(500).json({ error: 'Error al conectar con PeopleSoft' });
  }
});



module.exports = router;
