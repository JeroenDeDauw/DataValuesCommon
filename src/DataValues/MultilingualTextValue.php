<?php

namespace DataValues;

/**
 * Class representing a multilingual text value.
 *
 * @since 0.1
 *
 * @license GPL-2.0+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MultilingualTextValue extends DataValueObject {

	/**
	 * Array with language codes pointing to their associated texts.
	 *
	 * @var MonolingualTextValue[]
	 */
	private $texts = [];

	/**
	 * @param MonolingualTextValue[] $monolingualValues
	 *
	 * @throws IllegalValueException
	 */
	public function __construct( array $monolingualValues ) {
		foreach ( $monolingualValues as $monolingualValue ) {
			if ( !( $monolingualValue instanceof MonolingualTextValue ) ) {
				throw new IllegalValueException( 'Can only construct MultilingualTextValue from MonolingualTextValue objects' );
			}

			$languageCode = $monolingualValue->getLanguageCode();

			if ( array_key_exists( $languageCode, $this->texts ) ) {
				throw new IllegalValueException( 'Can only add a single MonolingualTextValue per language to a MultilingualTextValue' );
			}

			$this->texts[$languageCode] = $monolingualValue;
		}
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize( $this->texts );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @param string $value
	 */
	public function unserialize( $value ) {
		$this->__construct( unserialize( $value ) );
	}

	/**
	 * @see DataValue::getType
	 *
	 * @return string
	 */
	public static function getType() {
		return 'multilingualtext';
	}

	/**
	 * @see DataValue::getSortKey
	 *
	 * @return string|float|int
	 */
	public function getSortKey() {
		return empty( $this->texts ) ? '' : reset( $this->texts )->getSortKey();
	}

	/**
	 * Returns the texts as an array of monolingual text values.
	 *
	 * @return MonolingualTextValue[]
	 */
	public function getTexts() {
		return $this->texts;
	}

	/**
	 * Returns the multilingual text value
	 * @see DataValue::getValue
	 *
	 * @return $this
	 */
	public function getValue() {
		return $this;
	}

	/**
	 * @see DataValue::getArrayValue
	 *
	 * @return mixed
	 */
	public function getArrayValue() {
		$values = [];

		/**
		 * @var MonolingualTextValue $text
		 */
		foreach ( $this->texts as $text ) {
			$values[] = $text->getArrayValue();
		}

		return $values;
	}

	/**
	 * Constructs a new instance from the provided data. Required for @see DataValueDeserializer.
	 * This is expected to round-trip with @see getArrayValue.
	 *
	 * @deprecated since 0.3.2. Static DataValue::newFromArray constructors like this are
	 *  underspecified (not in the DataValue interface), and misleadingly named (should be named
	 *  newFromArrayValue). Instead, use DataValue builder callbacks in @see DataValueDeserializer.
	 *
	 * @param mixed $data Warning! Even if this is expected to be a value as returned by
	 *  @see getArrayValue, callers of this specific newFromArray implementation can not guarantee
	 *  this. This is not even guaranteed to be an array!
	 *
	 * @throws IllegalValueException if $data is not in the expected format. Subclasses of
	 *  InvalidArgumentException are expected and properly handled by @see DataValueDeserializer.
	 * @return static
	 */
	public static function newFromArray( $data ) {
		if ( !is_array( $data ) ) {
			throw new IllegalValueException( "array expected" );
		}

		$values = [];

		foreach ( $data as $monolingualValue ) {
			$values[] = MonolingualTextValue::newFromArray( $monolingualValue );
		}

		return new static( $values );
	}

}
