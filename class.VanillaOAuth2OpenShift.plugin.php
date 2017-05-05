<?php
/**
 * @copyright 2017 Balazs Miklos <mbalazs@gmail.com>
 * @license GPL
 */


$PluginInfo['VanillaOAuth2OpenShift'] = array(
    'Name' => 'OpenShift SSO',
    'ClassName' => "VanillaOAuth2OpenShiftPlugin",
    'Description' => 'Authenticate through an OpenShift 3 OAuth server.',
    'Version' => '1.0.0',
    'RequiredApplications' => array('Vanilla' => '2.2'),
    'SettingsUrl' => '/settings/VanillaOAuth2OpenShift',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'MobileFriendly' => true,
    'Icon' => 'oauth2.png',
    'Author' => "Miklos Balazs",
    'AuthorEmail' => 'mbalazs@gmail.com',
    'AuthorUrl' => 'https://www.podspace.io'
);
/*
$PluginInfo['OAuth2OpenShift'] = array(
    'Name' => 'OpenShift SSO',
    'ClassName' => "OAuth2OpenShiftPlugin",
    'Description' => 'Authenticate through an OpenShift 3 OAuth server.',
    'Version' => '1.0.0',
    'RequiredApplications' => array('Vanilla' => '2.2'),
    'SettingsUrl' => '/settings/OAuth2OpenShift',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'MobileFriendly' => true,
    'Icon' => 'oauth2.png',
    'Author' => "Miklos Balazs",
    'AuthorEmail' => 'mbalazs@gmail.com',
    'AuthorUrl' => 'https://www.podspace.io'
);
 */

/**
 * Class OAuth2OpenShiftPlugin
 *
 * Expose the functionality of the core class Gdn_OAuth2 to create SSO workflows.
 */

class VanillaOAuth2OpenShiftPlugin extends Gdn_OAuth2 {
    /**
     * @var string Sets the settings view in the dashboard.
     */
    protected $settingsView = 'settings/VanillaOAuth2OpenShift';

    /**
     * Set proper JWT authentication header as required by the OpenShift API
     */
    public function getProfileRequestOptions() {
        return ["Authorization-Header-Message" => "Bearer $this->accessToken"];
    }

    /**
     *   Allow the admin to input the keys that their service uses to send data.
     *
     * @param array $rawProfile profile as it is returned from the provider.
     *
     * @return array Profile array transformed by child class or as is.
     */
    public function translateProfileResults($rawProfile = []) {
        $provider = $this->provider();
        $email = val('ProfileKeyEmail', $provider, 'email');
        $translatedKeys = [
            'Email' => val('ProfileKeyEmail', $provider, 'email'),
            'Photo' => val('ProfileKeyPhoto', $provider, 'picture'),
            'Name' => val('ProfileKeyName', $provider, 'displayname'),
            'FullName' => val('ProfileKeyFullName', $provider, 'name'),
            'UniqueID' => val('ProfileKeyUniqueID', $provider, 'user_id')
        ];

        $profile = [];
        foreach($translatedKeys as $key => $value) {
          $profile[$key] = $this->deepLookup($rawProfile, $value);
        }

        $profile['Provider'] = $this->providerKey;

        return $profile;
    }

    protected function deepLookup($array, $key) {
        if(isset($array[$key])) {
          return $array[$key];
        } else if(strpos($key, '.') !== false) {
          $pieces = explode(".", $key, 2);
          return $this->deepLookup($array[$pieces[0]], $pieces[1]);
        } else {
          return null;
        }
    }

    /**
     * Set the key for saving OAuth settings in GDN_UserAuthenticationProvider
     */
    public function __construct() {
        $this->setProviderKey('VanillaOAuth2OpenShift');
    }
}
