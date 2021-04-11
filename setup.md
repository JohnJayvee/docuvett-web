##Stripe

1. Create account `https://stripe.com/`.

2. Then in `https://dashboard.stripe.com` create new account.

3. In `https://dashboard.stripe.com/account/apikeys` stored api and secret keys.

4. To `.env` add 
    ```
       STRIPE_SECRET=
       STRIPE_KEY=
       STRIPE_CLIENT_NAME=
       STRIPE_GRACE_PERIOD=
    ```
5. In `modular.php` stored information about modules. For example:
    ```
    'Contacts' => [
        'name' => 'Contacts',
        'model' => App\Models\Contact\Contact::class
    ],
    ```
    If module is free add  `'free' => true`.
    
    If module have metered plan add `'type' => App\Models\Module\Module::TYPE_METERED`.
    
6. In `stripe.php` stored default information about plans and webhooks. For correct set webhooks in `.env` need add `APP_URL=` (in local development you can use `https://ngrok.com` or etc.)

7. For import data to stripe use `php artisan stripe:import`.

## Xero
#### Create API keys
1. Log into xero https://login.xero.com/
2. In a new tab open xero developer portal and navigate to `My Apps` - https://developer.xero.com/myapps/
3. Create a 'New App' (Button located top right corner of screen)
     - Fill in the form
     - Set the OAuth 2 redirect URI. `/api/xero/callback`
     - Example: `https://niagara-demo.aws.hicalibertest.com.au/api/xero/callback`
#### Configure API keys
1. Add API keys to `.env`
- `XERO_CLIENT_ID=`
- `XERO_CLIENT_SECRET=`
- `XERO_CALLBACK_URL=`

Example
```
XERO_CLIENT_ID=51C161A39C854B49A38D49E62A783749
XERO_CLIENT_SECRET=CRri53u3Y5WtF8SWD8GqAEWPrUva7Asduq81zL9knE6SOG7d
XERO_CALLBACK_URL=https://niagara-demo.aws.hicalibertest.com.au/api/xero/callback
```
#### Authorization & Authentication
1. Authorize Xero access
- The Xero account holder needs to perform a one time Authorization to access their Xero API
    - Log in to App
    - Navigate to `Accounting > Xero`
    - In the Authorize tab, click on the `Authorize Xero` button
    - Follow the prompts to give access
    
All xero functions within the App will now work for anyone logged in with sufficient permissions.
#### Extending Xero Api Library
We are using the recommended SDK according to Xero docs
`https://github.com/XeroAPI/xero-php-oauth2`

At the moment it only has partial support for
- Accounting

In `App/Libraries/XeroApi` we have extended support for
- Payroll

If you need to extend support any further you can do so by:
- Download the appropriate yaml file from `https://github.com/XeroAPI/Xero-OpenAPI`
- Create/Login to SwaggerHub
    - `Create new > Import and Document API`
    - Select the yaml file to import and proceed
    - Click on `Export > Codegen Options`
        - Under Clients select `php`
            - packagePath: `XeroApi`
            - apiPackage: `App\Libraries\XeroApi\Api`
            - invokerPackage: `App\Libraries\XeroApi`
            - modelPackage: `Models\{Payroll}`
    - Click on `Export > Client SDK > php`
    - Add files to relevant folders in `App/Libraries/XeroApi`
    
## G-Suite
1. Create Service Account Key https://console.developers.google.com/apis/credentials
2. Go to https://console.developers.google.com/iam-admin/serviceaccounts and ensure that "G Suite Domain-wide Delegation" is enabled
3. Download JSON file with credentials
4. Move JSON file with credentials to `/keys/google_service_account.json`
5. Setup `GOOGLE_SERVICE_ACCOUNT_CREDENTIALS=/keys/google_service_account.json` in `.env`
6. Setup `GOOGLE_SERVICE_ACCOUNT_SUBJECT=` as Admin's email address
7. Authorise Service Account https://developers.google.com/identity/protocols/OAuth2ServiceAccount#delegatingauthority with scopes:
    - `https://www.googleapis.com/auth/calendar`
    - `https://www.googleapis.com/auth/drive` 
    - `https://mail.google.com/` 

_Note: Use service account Client ID for Client Name field_

## Firebase
1. Go to https://console.firebase.google.com/
2. Create or Import Google project
3. In project Dashboard click `Project settings` (top left corner)
4. Add new Web App in the `Your apps` section
5. Setup `FIREBASE_SENDER_ID`, `FIREBASE_PROJECT_ID`, `FIREBASE_API_KEY`, `FIREBASE_APP_ID` in `.env` from the given config
6. Click `Continue to console` button and go to `Cloud Messaging` tab
7. Generate Web Push Certificate key pair and setup `FIREBASE_VAPID_KEY`
8. Go to `Service accounts` tab and click `Generate new private key`
9. Download JSON file and specify path in `GOOGLE_FIREBASE_CREDENTIALS` (for example: **/keys/firebase_credentials.json**)

## Twillio
1. Buy a number (if needed) on https://www.twilio.com/console/phone-numbers/incoming
2. Setup this number to `TWILIO_SENDER_NUMBER` in `.env`
3. Go to https://www.twilio.com/console/project/settings
4. Setup `TWILIO_SID` and `TWILIO_TOKEN` in `.env`
5. Generate key `TWILIO_INCOMING_AUTH_TOKEN`
6. Go to https://www.twilio.com/console/phone-numbers/incoming and click created phone number
7. For messaging setup Webhook for **A MESSAGE COMES IN**:
    7. method: **POST**
    7. url: **/sms/twilio-webhook?incoming_auth_token={TWILIO_INCOMING_AUTH_TOKEN}**
    

## Microsoft
### Office 365
#### 1. Developer account
[Register developer account](https://developer.microsoft.com/en-US/microsoft-365/dev-program)

After register go to block 2.

#### 2. Create auth application 
[Register application](https://docs.microsoft.com/en-us/graph/auth-register-app-v2#register-a-new-application-using-the-azure-portal)

https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade

2.1) Sign in to the Azure portal using either a work or school account or a personal Microsoft account.

2.2) If your account gives you access to more than one tenant, select your account in the top right corner, and set your portal session to the Azure AD tenant that you want.

2.3) In the left-hand navigation pane, select the Azure Active Directory service, and then select App registrations > New registration.

2.4) When the Register an application page appears, enter your application's registration information:

2.4.1) Name - Enter a meaningful application name that will be displayed to users of the app.

2.4.2) Supported account types - Select which accounts you would like your application to support.
Preferable: `Accounts in any organizational directory (Any Azure AD directory - Multitenant) and personal Microsoft accounts (e.g. Skype, Xbox)`

2.4.3) Redirect URI - Select the type of app you're building, Web or Public client (mobile & desktop), and then enter the redirect URI (or reply URL) for your application.
Type must be `Web`.
- Example: `https://niagara-demo.aws.hicalibertest.com.au/365/callback`

Put that redirect URL into `MICROSOFT_GRAPH_CALLBACK_URL` env variable.

To add Client ID and Secret do next steps:
- On the page `Overview` (left side) copy `Application (client) ID` into `MICROSOFT_GRAPH_CLIENT_ID` env variable.
- On the page `Certificates & secrets` click on the button `New client secret`, select the term of key (`Never` is ok) and press `Add`.
- Copy `Value` of created secret key into `MICROSOFT_GRAPH_SECRET` env variable.

### Blocks 3 and 4 are needed for development
#### 3. Create access token
[Access token](https://docs.microsoft.com/en-us/graph/auth/auth-concepts)
#### 4. API Documentation
[Official documentation](https://docs.microsoft.com/en-us/graph/overview)
