# SUPR
## Introduction
### What does it do?
This extension renders your configured widgets for products from the SUPR management tool into your TYPO3 frontend.
An editor can add a special content element to select such widgets for rendering.

### Need Support?
Before the SUPR content can be rendered correctly you have to add your product widgets to the SUPR management tool.
If you need support for the SUPR products, please refer to
* https://de.supr.com/hilfe/
* https://support.supr.com/

#### Known problems:
1. `Widget id 0 is unavailable` -> The content element isn't configured correctly. Open the content element and change the widget.
2. `array_keys() expects parameter 1 to be array, null given` -> Your SUPR login data is not configured yet. An administrator has to add them to the extension configuration.

---
## For Editors
### Add the plugin
Switch to the page module.
To include the SUPR content element you have to add it to your desired page.
You can find it here: `Special Elements -> SUPR Widget`.

### Configure the plugin
After creating the content element, you choose between different widget-styles. These styles are configured in the SUPR managment tool.
In the content element, you will see a preview of your widget.

---
## For administrators
### Installation

The extension may be installed by using the Extension Manager in the TYPO3 backend:

1. Head to the TYPO3 backend module "Extension Manager"
2. Get the extension

   1. **Get it by the extension manager:** Select "Get Extensions" from the top and search for *supr*. Afterwards, click the button "Import and Install"
   1. **Get it from typo3.org:** You can always get current version from `http://typo3.org/extensions/repository/view/supr/current/` by downloading either the t3x or zip version. Upload the file afterwards in the Extension Manager.

### Configuration
After installation, you have to configure the extension. Switch to the Extension Manager and click on the extension name.

#### Login
Enter the credentials of your account from the SUPR management tool. With these credentials, the extension will authenticate with the API by SUPR and fetch all configured widgets.