# Introduction

API reference for the ACTA Chemical website and mobile e-commerce application.

<aside>
    <strong>Base URL</strong>: <code>{{ rtrim(config('app.url'), '/') }}</code>
</aside>

    The versioned mobile API is available under <code>/api/v1</code>. Commerce amounts use EGP and are returned both as formatted values and integer piastres where applicable.

    Protected endpoints require a Laravel Sanctum token. Register or log in, then send the returned token using the <code>Authorization: Bearer YOUR_TOKEN</code> header.

    <aside>Use Paymob test credentials during development. Payment success must be confirmed by the authenticated backend callback.</aside>

