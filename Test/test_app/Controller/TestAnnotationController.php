<?php
App::uses('Controller', 'Controller');

class TestAnnotationController extends Controller {

/**
 * @roles all
 */
	public function index() {
	}

/**
 * @roles anonymous
 */
	public function anonymous() {
	}

/**
 * @roles authenticated
 */
	public function view() {
	}

/**
 * @roles admin
 */
	public function add() {
	}

/**
 * @roles admin, manager
 */
	public function administrative() {
	}

/**
 * @roles ["admin", "manager", "ceo"]
 */
	public function administrative_two() {
	}

}
