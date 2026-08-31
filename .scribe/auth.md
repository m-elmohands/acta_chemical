# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_SANCTUM_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Create a token with <code>POST /api/v1/auth/register</code> or <code>POST /api/v1/auth/login</code>. Public catalog endpoints do not require authentication.
