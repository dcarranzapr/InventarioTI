from django.urls import path, include
from rest_framework.routers import DefaultRouter
# Importar vistas de API

router = DefaultRouter()
# Registrar rutas aquí

urlpatterns = [
    path('', include(router.urls)),
    # Otras rutas de API
]
