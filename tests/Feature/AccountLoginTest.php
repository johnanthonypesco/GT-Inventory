<?php


describe('Customer Account Operations', function () {
    it('can access the login page.', function () {
        $fuck = $this->get('/login');
    
        $fuck->assertStatus(200);
    });
    
})->group('customer');
