import React from "react";
import Button from "../button";

export default class PostlistItem extends React.Component {
	static defaultProps = {
		id: "",
		title: "",
		type: "",
		onRemove: () => false
	};

	render() {
		const {id, title, type, onRemove} = this.props;

		return (
			<tr>
				<td><strong>{title}</strong></td>
				<td>{type}</td>
				<td className="wds-postlist-item-remove">
					<Button
						color="red"
						icon="sui-icon-trash"
						onClick={() => onRemove(id)}
					/>
				</td>
			</tr>
		);
	}
}
