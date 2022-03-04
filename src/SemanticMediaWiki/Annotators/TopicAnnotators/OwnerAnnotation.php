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

namespace SemanticStructuredDiscussions\SemanticMediaWiki\Annotators\TopicAnnotators;

use Flow\Exception\CrossWikiException;
use Flow\Exception\InvalidInputException;
use SMW\DIProperty;
use SMW\SemanticData;

/**
 * This annotation contains information about the owner of a topic.
 */
class OwnerAnnotation extends TopicAnnotator {
	/**
	 * @inheritDoc
	 */
	public function addAnnotation( SemanticData $semanticData ): void {
		try {
			$topicArticle = $this->topic->getTopicOwner();
		} catch ( CrossWikiException | InvalidInputException $e ) {
			// Silently ignore specifically these exceptions
			return;
		}

		$semanticData->addPropertyObjectValue(
			new DIProperty( self::getId() ),
			new \SMWDIWikiPage( $topicArticle->getDBkey(), $topicArticle->getNamespace() )
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function getId(): string {
		return '__sd_topic_owner';
	}

	/**
	 * @inheritDoc
	 */
	public static function getLabel(): string {
		return 'Topic owner';
	}

	/**
	 * @inheritDoc
	 */
	public static function getDefinition(): array {
		return [
			'label' => self::getLabel(),
			'type' => '_wpg',
			'viewable' => true,
			'annotable' => false
		];
	}
}