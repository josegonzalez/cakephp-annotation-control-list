<?php
/**
 * All AllAnnotationControlList plugin tests
 */
class AllAnnotationControlListTest extends CakeTestCase {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All AllAnnotationControlList test');
		$path = CakePlugin::path('AnnotationControlList') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);
		return $suite;
	}

}
