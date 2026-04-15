<?php

it('landing page loads without smoke issues in a real browser', function () {
    visit('/')->assertNoSmoke();
});

it('booking page loads without smoke issues in a real browser', function () {
    visit('/booking')->assertNoSmoke();
});

it('booking success page loads without smoke issues in a real browser', function () {
    visit('/booking/success')->assertNoSmoke();
});
