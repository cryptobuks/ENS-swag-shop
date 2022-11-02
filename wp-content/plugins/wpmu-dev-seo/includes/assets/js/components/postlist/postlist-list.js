import React from "react";
import {__} from "@wordpress/i18n";
import PostlistItem from "./postlist-item";
import PostlistModal from "./postlist-modal";
import Notice from "../notice";
import Button from "../button";
import RequestUtil from "../utils/request-util";
import NoticeUtil from "../utils/notice-util";
import update from "immutability-helper";
import FloatingNoticePlaceholder from "../floating-notice-placeholder";

export default class PostlistList extends React.Component {
	static defaultProps = {
		items: [],
		postTypes: {},
		nonce: "",
		onUpdate: () => false,
		onRemove: () => false
	};

	constructor(props) {
		super(props);

		this.state = {
			loading: true,
			openDialog: false,
			items: this.props.items,
			posts: []
		};
	}

	componentDidMount() {
		this.loadPosts();
	}

	render() {
		const {postTypes, items, nonce, onRemove} = this.props;
		const {posts, loading, openDialog} = this.state;

		return (
			<div className="wds-postlist-list">
				<FloatingNoticePlaceholder id="wds-postlist-notice"/>

				{!loading && (
					<React.Fragment>
						{!!items.length && (
							<table className="wds-postlist sui-table">
								<thead>
								<tr>
									<th>{__("Post", "wds")}</th>
									<th colSpan="2">{__("Post Type", "wds")}</th>
								</tr>
								</thead>
								<tbody>
								{posts.filter(post => items.indexOf(post.id) !== -1).map((post, index) => (
									<PostlistItem
										key={index} onRemove={(id) => onRemove(id)} {...post}
									/>
								))}
								</tbody>
							</table>
						)}

						{!items.length && (
							<Notice type="info" message={__("You haven't chosen to exclude any posts/pages.", "wds")}/>
						)}

						<Button
							id="wds-postlist-selector-open"
							icon="sui-icon-plus"
							text={__("Add Exclusion", "wds")}
							onClick={() => this.toggleModal()}
						/>
					</React.Fragment>
				)}

				{loading && (
					<small><i>{__("Loading posts, please hold on", "wds")}</i></small>
				)}

				{openDialog && (
					<PostlistModal
						id="wds-postlist-selector"
						postTypes={postTypes}
						onPostsUpdate={(posts) => this.handlePostsUpdate(posts)}
						onSubmit={(values) => this.handleItemsUpdate(values)}
						onClose={() => this.toggleModal()}
						nonce={nonce}
					/>
				)}
			</div>
		);
	}

	loadPosts() {
		RequestUtil
			.post("wds-load_exclusion_posts-posts_data-specific", this.props.nonce, {
				type: "exclude",
				posts: this.props.items
			})
			.then((data) => {
				this.setState({posts: data.posts, loading: false});
			})
			.catch((error) => {
				NoticeUtil.showErrorNotice(
					"wds-postlist-notice",
					error || __('An error occurred. Please try again.', 'wds'),
					false
				);

				this.setState({loading: false});
			});
	}

	handleItemsUpdate(values) {
		let {items} = this.props;

		values.forEach(value => {
			const intVal = parseInt(value);
			if (items.indexOf(intVal) === -1) {
				items = update(items, {$push: [intVal]});
			}
		});

		this.setState({openDialog: false});
		this.props.onUpdate(items);
	}

	handlePostsUpdate(updatablePosts) {
		let {posts} = this.state;

		if (!posts) {
			posts = [];
		}

		const ids = posts ? posts.map(post => post.id) : [];

		updatablePosts.forEach(post => {
			if (ids.indexOf(post.id) === -1) {
				posts = update(posts, {$push: [post]});
			}
		});

		this.setState({posts: posts});
	}

	toggleModal() {
		this.setState({openDialog: !this.state.openDialog});
	}
}
