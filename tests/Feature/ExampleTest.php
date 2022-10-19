<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ExampleTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBasicTest()
	{

		$this->seed()
			->get('/')
			->assertStatus(200);
	}
}
