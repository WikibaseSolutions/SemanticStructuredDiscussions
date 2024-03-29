<?php declare( strict_types=1 );
/**
 * Semantic Structured Discussions MediaWiki extension
 * Copyright (C) 2022  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace SemanticStructuredDiscussions\SemanticMediaWiki\Annotators\ReplyAnnotators;

use SemanticStructuredDiscussions\SemanticMediaWiki\Annotators\Annotator;
use SemanticStructuredDiscussions\StructuredDiscussions\SDTopic;
use SemanticStructuredDiscussions\StructuredDiscussions\SDReply;

abstract class ReplyAnnotator implements Annotator {
	/**
	 * @var SDReply The reply-information to use for annotating
	 */
	protected SDReply $reply;

	/**
	 * @var SDTopic The topic-information to use for annotating
	 */
	protected SDTopic $topic;

	/**
	 * @param SDReply $reply The reply-information to use for annotating
	 * @param SDTopic $topic The topic-information to use for annotating
	 */
	public function __construct( SDReply $reply, SDTopic $topic ) {
		$this->reply = $reply;
		$this->topic = $topic;
	}
}
