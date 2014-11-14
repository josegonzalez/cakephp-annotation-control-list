<?php
App::uses('Controller', 'Controller');

class TestModelAnnotationController extends Controller {

	public function none() {
	}

/**
 * @isAuthorized.roles all
 */
	public function index() {
	}

/**
 * @isAuthorized.roles anonymous
 */
	public function anonymous() {
	}

/**
 * @isAuthorized.roles authenticated
 */
	public function view() {
	}

/**
 * @isAuthorized.roles admin
 */
	public function add() {
	}

/**
 * @isAuthorized.roles admin, manager
 */
	public function administrative() {
	}

/**
 * @isAuthorized.roles ["admin", "manager", "ceo"]
 */
	public function administrative_two() {
	}

/**
 * @isAuthorized.roles authenticated
 */
	public function missing_finder() {
	}

/**
 * @isAuthorized.roles authenticated
 * @isAuthorized.model Post
 * @isAuthorized.find first
 */
	public function has_finder() {
	}

/**
 * @isAuthorized.roles authenticated
 * @isAuthorized.always ["group", "admin"]
 * @isAuthorized.model Post
 * @isAuthorized.find first
 */
	public function always_if_admin() {
	}

/**
 * @isAuthorized.roles authenticated
 * @isAuthorized.always ["group", "admin"]
 * @isAuthorized.model Post
 * @isAuthorized.find first
 * @isAuthorized.conditions.if ["group", "group_name"]
 */
	public function if_conditions() {
	}

}
