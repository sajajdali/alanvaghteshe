<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swagger سبد خرید هوشمند</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; background: #fafafa; }
        .swagger-ui .topbar { display: none; }
        .swagger-ui { direction: ltr; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    window.addEventListener('load', function () {
        let swaggerUi;
        swaggerUi = SwaggerUIBundle({
            url: @json(route('shopping-list.docs.specification')),
            dom_id: '#swagger-ui',
            deepLinking: true,
            persistAuthorization: true,
            displayRequestDuration: true,
            tryItOutEnabled: true,
            responseInterceptor: function (response) {
                if (response.url && /\/v1\/(verify|register)(\?|$)/.test(response.url)) {
                    try {
                        const body = typeof response.data === 'string'
                            ? JSON.parse(response.data)
                            : response.obj || response.data;
                        if (body && body.token) {
                            swaggerUi.preauthorizeApiKey('sanctumBearer', body.token);
                        }
                    } catch (error) {
                        console.warn('Swagger could not save the login token.', error);
                    }
                }
                return response;
            },
            presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
            layout: 'BaseLayout'
        });
    });
</script>
</body>
</html>
