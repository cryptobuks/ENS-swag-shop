import React from 'react';
import {render} from 'react-dom';
import ErrorBoundary from "./components/error-boundry";
import {Provider} from "react-redux";
import Config_Values from "./es6/config-values";
import {initializeTypes, validateTypes} from "./components/schema/utils/type-utils";
import {createStore} from "redux";
import reducer from "./components/schema/reducers/";
import domReady from "@wordpress/dom-ready";
import SchemaTypesBuilderContainer from "./components/schema/components/schema-types-builder";

domReady(() => {
	const schemaBuilderPlaceholder = document.getElementById('wds-schema-type-components');
	if (schemaBuilderPlaceholder) {
		const savedTypeData = Config_Values.get('types', 'schema_types');
		const store = createStore(reducer, {types: validateTypes(initializeTypes(savedTypeData))});

		render(
			<Provider store={store}>
				<ErrorBoundary><SchemaTypesBuilderContainer/></ErrorBoundary>
			</Provider>,
			schemaBuilderPlaceholder
		);
	}
});
