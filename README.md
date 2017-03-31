# VanillaOAuth2OpenShift
OAuth2 plugin for Vanilla Forums compatible with OpenShift

## Configuring OpenShift

First of all, a new "oauthclient" resource has to be defined for your Vanilla forum
to be able to authenticate through OpenShift.

Use this command to create the oauthclient:
````
oc create -f - <<EOF
apiVersion: v1
grantMethod: auto
kind: OAuthClient
metadata:
  creationTimestamp: null
  name: vanilla
redirectURIs:
- https://<YOUR VANILLA HOSTNAME>/entry/VanillaOAuth2OpenShift
secret: <OAUTH SECRET>
EOF
````

Just replace <YOUR VANILLA HOSTNAME> with the hostname of your Vanilla Forum, and <OAUTH SECRET> with an
arbitraty shared secret to use for authenticating between the servers (Vanilla and OpenShift).

## Configuring Vanilla

First, check out the contents of this repo under the "plugins" directory of your Vanilla setup:

````
cd /var/www/vanilla/plugins && git clone https://github.com/mbalazs/VanillaOAuth2OpenShift
````

1. Sign in as "admin" in Vanilla, and navigate to the Dashboard and click on "Settings".
2. Enable the "OpenShift SSO" plugin.
3. Click the configuration icon, and specify the following settings:

| Configuration Key | Value                                                                       |
| ----------------- | --------------------------------------------------------------------------- |
| Client ID         | vanilla (actually the metadata.name of the OpenShift OAuthClient definition |
| Secret            | The same secret you have set in OpenShift                                   |
| Authorize URL     | https://\<openshift master\>                                                  |
| Token URL         | https://\<openshift master\>/oauth/token                                      |
| Profile URL       | https://\<openshift master\>/oapi/v1/users/~                                  |
| Register URL      |                                                                             |
| Sign out URL      |                                                                             |
| Request scope     | user:full                                                                   |
| Email             | The attribute that contains the email address in 'oc export user', e.g. "metadata.email" |
| Photo             |                                                                             |
| Display Name      | The attribute that contains the full name of the user, e.g. "fullName"      |
| Full Name         | Same as above                                                               |
| User ID           | Same as above, e.g. "metadata.name"                                         |

4. Check the "Make this connection your default sign in method" checkbox
5. You're set to go!

Note: you will have to access your Vanilla Forums site through *https*, or else you will get an error.
