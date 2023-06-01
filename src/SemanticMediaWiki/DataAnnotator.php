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

namespace SemanticStructuredDiscussions\SemanticMediaWiki;

use SemanticStructuredDiscussions\StructuredDiscussions\SDReply;
use SemanticStructuredDiscussions\StructuredDiscussions\SDTopic;
use SMW\SemanticData;
use SMW\Subobject;
use SMW\DIProperty;
use Title;
use SMWDIContainer;

/**
 * This class is responsible for annotating the SemanticData object with information about
 * the given topic and its replies.
 */
class DataAnnotator {
	/**
	 * @var AnnotatorStore
	 */
	private AnnotatorStore $annotatorStore;

	/**
	 * @param AnnotatorStore $annotatorStore
	 */
	public function __construct( AnnotatorStore $annotatorStore ) {
		$this->annotatorStore = $annotatorStore;
	}

	/**
	 * Adds annotations to the given SemanticData object about the given Topic.
	 *
	 * @param SDTopic $topic The topic about which to add annotations
	 * @param SemanticData $semanticData The SemanticData object to add the annotations to
	 */
	public function addAnnotations( SDTopic $topic, SemanticData $semanticData ): void {
		if ( !$topic->isEveryoneAllowed() ) {
			// Do not annotate the topic if it is not viewable by everyone, since this WILL lead to information leakage
			return;
		}

		$this->addTopicAnnotations( $topic, $semanticData );
		$this->addRepliesAnnotations( $topic->getReplies(), $semanticData, $topic );
	}

	/**
	 * Add the given replies as subobjects to the given SemanticData object.
	 *
	 * @param SDReply[] $replies
	 * @param SemanticData $semanticData
	 * @param SDTopic $topic
	 */
	private function addRepliesAnnotations( array $replies, SemanticData $semanticData, SDTopic $topic ): void {
		foreach ( $replies as $reply ) {
			if ( !$reply->isEveryoneAllowed() ) {
				// Do not annotate the reply if it is not viewable by everyone, since this WILL lead to
				// information leakage
				continue;
			}

			$name = sprintf( 'flow-post-%s', $reply->getPostId() );
			if ( $semanticData->hasSubSemanticData( $name ) ) {
				$subobjectSemanticData = $semanticData->findSubSemanticData( $name );
			} else {
				$subobject = new Subobject( $semanticData->getSubject()->getTitle() );
				$subobject->setEmptyContainerForId( $name );
				$subobjectSemanticData = $subobject->getSemanticData();
			}

			$this->addReplyAnnotations( $reply, $subobjectSemanticData, $topic );

			// See SemanticData::addSubobject
			$semanticData->addPropertyObjectValue(
				new DIProperty( DIProperty::TYPE_SUBOBJECT ),
				new SMWDIContainer( $subobjectSemanticData )
			);
		}
	}

	/**
	 * Add annotations about the given topic to the given SemanticData object.
	 *
	 * @param SDTopic $topic
	 * @param SemanticData $semanticData
	 */
	private function addTopicAnnotations( SDTopic $topic, SemanticData $semanticData ): void {
		$topicAnnotators = $this->annotatorStore->getTopicAnnotators( $topic );

		foreach ( $topicAnnotators as $annotator ) {
			$annotator->addAnnotation( $semanticData );
		}
	}

	/**
	 * Add annotations about the given reply to the given SemanticData object.
	 *
	 * @param SDReply $reply
	 * @param SemanticData $semanticData
	 * @param SDTopic $topic
	 */
	private function addReplyAnnotations( SDReply $reply, SemanticData $semanticData, SDTopic $topic ): void {
		$replyAnnotators = $this->annotatorStore->getReplyAnnotators( $reply, $topic );

		foreach ( $replyAnnotators as $annotator ) {
			$annotator->addAnnotation( $semanticData );
		}
	}
}
