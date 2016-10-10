<?php
/**
 * - Spl's strength lies in the ability to complete complex tasks such as sorting
 *   and processing xml/json data with minimum amount of code
 *
 * - when using recursive class, don't remember to chain into RecursiveIteratorIterator
 *   (except if you already chained RecursiveTreeIteraot, which extends
 *   RecursiveIteratorIterator)
 *
 * - when extends from a RecursiveFilterIterator, if you override __construct(), you
 *   will also need to override getChildren() method
 */