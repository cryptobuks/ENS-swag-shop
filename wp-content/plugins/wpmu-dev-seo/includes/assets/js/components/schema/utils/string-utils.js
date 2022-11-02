import {pluralize} from "@capaj/pluralize";

export function capitalizeWords(string) {
	return string
		// insert a space before all caps
		.replace(/([A-Z])/g, ' $1')
		// uppercase the first character
		.replace(/^./, (string) => {
			return string.toUpperCase();
		})
}

export function singular(word) {
	return pluralize.singular(word);
}
