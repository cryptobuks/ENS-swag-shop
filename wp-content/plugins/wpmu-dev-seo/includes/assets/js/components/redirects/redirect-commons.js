import Config_Values from "../../es6/config-values";

export function getDefaultRedirectType() {
	return Config_Values.get('default_redirect_type', 'autolinks');
}
