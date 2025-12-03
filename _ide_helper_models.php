<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email_address
 * @property string $phone_number
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 * @property string $customer_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCustomerId($value)
 * @mixin \Eloquent
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property string $property_id
 * @property string $plan_id
 * @property string $down_payment
 * @property string $customer_id
 * @property string $application_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer $customerData
 * @property-read \App\Models\MortgagePlan $mortgagePlanData
 * @property-read \App\Models\Property $propertyData
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereDownPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgageApplication whereUpdatedAt($value)
 */
	class MortgageApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $duration_months
 * @property string $interest_rate
 * @property string $plan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereDurationMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MortgagePlan whereUpdatedAt($value)
 */
	class MortgagePlan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withoutTrashed()
 * @mixin \Eloquent
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $price
 * @property string $location
 * @property string $property_id
 * @property string|null $description
 * @property string|null $images
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property withoutTrashed()
 * @mixin \Eloquent
 */
	class Property extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

