<?php

class Smartcrawl_Check_Focus_Stopwords extends Smartcrawl_Check_Post_Abstract {

	/**
	 * State.
	 *
	 * @var bool
	 */
	private $state;

	public function get_status_msg() {
		return false === $this->state
			? __( 'There are stop words in focus keywords', 'wds' )
			: __( 'Focus to the point', 'wds' );
	}

	public function apply() {
		$focus = $this->get_raw_focus();
		$state = true;
		foreach ( $focus as $phrase ) {
			$phrase = new Smartcrawl_String( $phrase, $this->get_language() );
			if ( ! $phrase->has_stopwords() ) {
				continue;
			}
			$state = false;
			break;
		}

		$this->state = $state;

		return ! ! $this->state;
	}

	public function get_recommendation() {
		$focus = $this->get_raw_focus();

		if ( count( $focus ) > 1 ) {
			$key_phrase = __( 'keywords or key phrases', 'wds' );
		} else {
			$subj       = end( $focus );
			$key_phrase = false === strpos( $subj, ' ' )
				? __( 'keywords', 'wds' )
				: __( 'key phrase', 'wds' );
		}
		$message = $this->state
			// translators: %s keywords or key phrase.
			? __( 'You kept the focus %s of your article to the point, way to go!', 'wds' )
			// translators: %s keywords or key phrase.
			: __( 'Your focus %s contains some words that might be considered insignificant in a search query.', 'wds' );

		return sprintf( $message, $key_phrase );
	}

	public function get_more_info() {
		return __( 'Stop words are words which can be considered insignificant in a search query, either because they are way too common, or because they do not convey much information. Such words are often filtered out from a search query. Ideally, you will want such words to not be a part of your article focus.', 'wds' );
	}
}