<?php
App::uses('Controller', 'Controller');

class TestAnnotationController extends Controller {

	public function none() {
	}

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

/**
 * @roles ["noprefix"]
 */
	public function action() {
	}

/**
 * @some_prefix.roles ["prefix"]
 */
	public function prefix_action() {
	}

}
