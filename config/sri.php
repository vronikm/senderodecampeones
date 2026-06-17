<?php

return [
    'ambiente' => (string) (getenv('SENDERO_SRI_AMBIENTE') ?: '1'),
    'tipo_emision' => '1',
    'version_factura' => '2.1.0',
    'iva_tarifa_default' => (float) (getenv('SENDERO_SRI_IVA_TARIFA') !== false ? getenv('SENDERO_SRI_IVA_TARIFA') : 0),
    'valores_incluyen_iva' => true,
    'emisor' => [
        'ruc' => getenv('SENDERO_SRI_RUC') ?: '1103269179001',
        'razon_social' => getenv('SENDERO_SRI_RAZON_SOCIAL') ?: 'CORDOVA ARIAS GERARDO ALFONSO',
        'nombre_comercial' => getenv('SENDERO_SRI_NOMBRE_COMERCIAL') ?: 'SENDERO DE CAMPEONES',
        'direccion_matriz' => getenv('SENDERO_SRI_DIRECCION_MATRIZ') ?: '24 DE MAYO 20989 Y AZUAY',
        'direccion_establecimiento' => getenv('SENDERO_SRI_DIRECCION_ESTABLECIMIENTO') ?: '24 DE MAYO 20989 Y AZUAY',
        'codigo_establecimiento' => getenv('SENDERO_SRI_ESTABLECIMIENTO') ?: '003',
        'punto_emision' => getenv('SENDERO_SRI_PUNTO_EMISION') ?: '004',
        'obligado_contabilidad' => getenv('SENDERO_SRI_OBLIGADO_CONTABILIDAD') ?: 'NO',
        'contribuyente_especial' => getenv('SENDERO_SRI_CONTRIBUYENTE_ESPECIAL') ?: '',
        'agente_retencion' => getenv('SENDERO_SRI_AGENTE_RETENCION') ?: '',
        'contribuyente_rimpe' => getenv('SENDERO_SRI_CONTRIBUYENTE_RIMPE') ?: '',
    ],
    'firma' => [
        'archivo' => dirname(__DIR__) . '/storage/certificados/firma.p12',
        'clave' => getenv('SENDERO_SRI_FIRMA_CLAVE') ?: '',
    ],
    'correo' => [
        'from' => getenv('SENDERO_FACTURACION_FROM') ?: 'senderodecampeones@outlook.com',
    ],
    'webservices' => [
        'pruebas' => [
            'recepcion' => 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl',
            'autorizacion' => 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl',
        ],
        'produccion' => [
            'recepcion' => 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl',
            'autorizacion' => 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl',
        ],
    ],
    'formas_pago' => [
        '01' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO',
        '15' => 'COMPENSACION DE DEUDAS',
        '16' => 'TARJETA DE DEBITO',
        '17' => 'DINERO ELECTRONICO',
        '18' => 'TARJETA PREPAGO',
        '19' => 'TARJETA DE CREDITO',
        '20' => 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO',
        '21' => 'ENDOSO DE TITULOS',
    ],
    'forma_pago_default' => getenv('SENDERO_SRI_FORMA_PAGO') ?: '20',
    'impuestos' => [
        'IVA' => [
            'codigo' => '2',
            'tarifas' => [
                '0' => ['codigo' => '0', 'porcentaje' => 0],
                '12' => ['codigo' => '2', 'porcentaje' => 12],
                '14' => ['codigo' => '3', 'porcentaje' => 14],
                '15' => ['codigo' => '4', 'porcentaje' => 15],
                'NO_OBJETO' => ['codigo' => '6', 'porcentaje' => 0],
                'EXENTO' => ['codigo' => '7', 'porcentaje' => 0],
            ],
        ],
    ],
    'storage' => [
        'xml_generados' => dirname(__DIR__) . '/storage/sri/xml/generados/',
        'xml_firmados' => dirname(__DIR__) . '/storage/sri/xml/firmados/',
        'xml_autorizados' => dirname(__DIR__) . '/storage/sri/xml/autorizados/',
        'ride' => dirname(__DIR__) . '/storage/sri/ride/',
        'logs' => dirname(__DIR__) . '/storage/sri/logs/',
        'certificados' => dirname(__DIR__) . '/storage/certificados/',
    ],
];
