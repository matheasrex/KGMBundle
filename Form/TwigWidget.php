<?php

namespace KGMBundle\Form;

/**
 * Twig field type list class
 */
class TwigWidget
{
	/**
	 * @const widget type names
	 */
	const WIDGET_TEXT = 'text';
	const WIDGET_TEXTAREA = 'textarea';
	const WIDGET_EMAIL = 'email';
	const WIDGET_INTEGER = 'integer';
	const WIDGET_MONEY = 'money';
	const WIDGET_NUMBER = 'number';
	const WIDGET_PASSWORD = 'password';
	const WIDGET_PERCENT = 'percent';
	const WIDGET_SEARCH = 'search';
	const WIDGET_URL = 'url';
	const WIDGET_CHOICE = 'choice';
	const WIDGET_ENTITY = 'entity';
	const WIDGET_COUNTRY = 'country';
	const WIDGET_LANGUAGE = 'language';
	const WIDGET_LOCALE = 'locale';
	const WIDGET_TIMEZONE = 'timezone';
	const WIDGET_DATE = 'date';
	const WIDGET_DATETIME = 'datetime';
	const WIDGET_TIME = 'time';
	const WIDGET_BIRTHDAY = 'birthday';
	const WIDGET_CHECKBOX = 'checkbox';
	const WIDGET_FILE = 'file';
	const WIDGET_RADIO = 'radio';
	const WIDGET_COLLECTION = 'collection';
	const WIDGET_REPEATED = 'repeated';
	const WIDGET_HIDDEN = 'hidden';
	const WIDGET_CSRF = 'csrf';
	const WIDGET_FIELD = 'field';
	const WIDGET_FORM = 'form';
	
	/**
	 * @const custom widget type names
	 */
	const CUSTOM_WIDGET_TAX = 'taxfield';
	const CUSTOM_WIDGET_BIT = 'bitfield';
}
