import React from "react";
import Button from "../button";
import Dropdown from "../dropdown";
import DropdownButton from "../dropdown-button";
import Pagination from "../pagination";
import PaginationUtil from "../utils/pagination-util";
import {__} from "@wordpress/i18n";
import CustomKeywordModal from "./custom-keyword-modal";
import update from "immutability-helper";

export default class CustomKeywordPairs extends React.Component {
	static defaultProps = {
		data: ''
	};

	constructor(props) {
		super(props);

		this.state = {
			pairs: this.dataPropToPairs(),
			addingPair: false,
			editingPair: false,
			currentPageNumber: 1,
		};

		this.pairsPerPage = 10;
	}

	render() {
		const pairs = this.state.pairs;
		const pairsCount = this.objectLength(pairs);
		const pairsExist = pairsCount > 0;
		const page = this.getPairsPage();

		return <div className="wds-keyword-pairs">
			{pairsExist &&
				<table className="wds-keyword-pairs-existing sui-table">
					<tbody>
					<tr>
						<th>{__('Keyword', 'wds')}</th>
						<th colSpan="2">{__('Auto-Linked URL', 'wds')}</th>
					</tr>

					{Object.keys(page).map(key => {
						const pair = page[key];
						return <tr className="wds-keyword-pair" key={key}>
							<td className="wds-pair-keyword">{pair.keyword}</td>
							<td className="wds-pair-url">{pair.url}</td>
							<td className="wds-pair-actions">
								<Dropdown buttons={[
									<DropdownButton onClick={() => this.startEditingPair(key)}
										icon="sui-icon-pencil"
										text={__('Edit', 'wds')}/>,
									<DropdownButton onClick={() => this.deletePair(key)}
										icon="sui-icon-trash"
										text={__('Delete', 'wds')} red={true}/>
								]}/>

								{this.state.editingPair === key &&
									<CustomKeywordModal
										keyword={pair.keyword}
										url={pair.url}
										editMode={true}
										onClose={() => this.stopEditingPair()}
										onSave={(keyword, url) => this.editPair(key, keyword, url)}
									/>
								}
							</td>
						</tr>;
					})}
					</tbody>
				</table>
			}

			<div className="wds-keyword-pairs-actions">
				<div className="wds-keyword-pair-new">
					<Button id="wds-keyword-pair-new-button"
						icon="sui-icon-plus"
						onClick={() => this.startAddingPair()}
						text={__('Add Link', 'wds')}/>
				</div>

				<React.Fragment>
					{pairsCount > this.pairsPerPage &&
						<Pagination count={pairsCount}
							currentPage={this.state.currentPageNumber}
							perPage={this.pairsPerPage}
							onClick={(pageNumber) => this.changePage(pageNumber)}/>
					}
				</React.Fragment>
			</div>

			<textarea name="wds_autolinks_options[customkey]"
				style={{display: "none"}}
				value={this.pairsToText()}
				readOnly={true}/>

			{this.state.addingPair &&
				<CustomKeywordModal
					onClose={() => this.stopAddingPair()}
					onSave={(keyword, url) => this.addPair(keyword, url)}
				/>
			}
		</div>;
	}

	dataPropToPairs() {
		return this.textToPairs(this.props.data);
	}

	objectLength(collectionObject) {
		return Object.keys(collectionObject).length;
	}

	changePage(pageNumber) {
		this.setState({currentPageNumber: pageNumber});
	}

	getPairsPage() {
		return PaginationUtil.getPage(
			this.state.pairs,
			this.state.currentPageNumber,
			this.pairsPerPage
		);
	}

	textToPairs(text) {
		const lines = text.split(/\n/);
		const pairs = [];
		lines.forEach((line) => {
			if (!line.includes(',')) {
				return;
			}
			const parts = line.split(',').map(part => part.trim());
			pairs.push({
				keyword: parts.slice(0, -1).join(','),
				url: parts.slice(-1).pop()
			});
		});

		return pairs;
	}

	pairsToText() {
		const lines = [];
		this.state.pairs.forEach((pair) => {
			const keyword = pair.keyword?.trim();
			const url = pair.url?.trim();

			if (keyword && url) {
				lines.push(keyword + ',' + url);
			}
		});

		return lines.join("\n");
	}

	startEditingPair(index) {
		this.setState({
			editingPair: index
		});
	}

	editPair(index, keyword, url) {
		const pairs = this.state.pairs.slice();
		if (!keyword.trim() || !url.trim()) {
			return;
		}
		pairs[index] = {
			keyword: keyword,
			url: url
		};

		this.setState({
			pairs: pairs,
			editingPair: false
		});
	}

	stopEditingPair() {
		this.setState({
			editingPair: false
		});
	}

	startAddingPair() {
		this.setState({
			addingPair: true
		});
	}

	addPair(keyword, url) {
		if (!keyword.trim() || !url.trim()) {
			return;
		}

		const pairs = this.state.pairs.slice();

		pairs.splice(0, 0, {
			keyword: keyword,
			url: url
		});

		this.setState({
			pairs: update(this.state.pairs, {$set: pairs}),
			addingPair: false,
			currentPageNumber: 1
		});
	}

	stopAddingPair() {
		this.setState({
			addingPair: false
		});
	}

	deletePair(index) {
		const pairs = this.state.pairs.filter((pair, idx) => idx !== index);
		this.setState({
			pairs: pairs
		});
	}
}
