const express = require('express');
const axios = require('axios');
const cors = require('cors');
const app = express();

app.use(cors());
app.use(express.json());

app.post('/api/soap', async (req, res) => {
  const { xmlBody, soapAction } = req.body;
  const endpoint = "http://10.8.17.131:8010/PSIGW/PeopleSoftServiceListeningConnector/PSFT_HR"; // <--- SOLO ESTO

  try {
    const response = await axios.post(endpoint, xmlBody, {
      headers: {
        "Content-Type": "text/xml;charset=UTF-8",
        ...(soapAction && { SOAPAction: `"${soapAction}"` }),
      }
    });
    res.status(200).send(response.data);
  } catch (err) {
    res.status(500).send(err.response ? err.response.data : err.toString());
  }
});

const PORT = 4000;
app.listen(PORT, () => {
  console.log(`SOAP Proxy escuchando en puerto ${PORT}`);
});
