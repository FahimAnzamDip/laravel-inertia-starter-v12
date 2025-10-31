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
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $email
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereUpdatedAt($value)
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereUpdatedAt($value)
 */
	class Child extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property string $discount_type
 * @property string $discount_value
 * @property string|null $minimum_purchase_amount
 * @property string|null $maximum_discount_amount
 * @property int $quantity_to_generate
 * @property int $usage_limit_per_code
 * @property string $valid_from
 * @property string $valid_until
 * @property int $is_active
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromoCode> $activePromoCodes
 * @property-read int|null $active_promo_codes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromoCode> $promoCodes
 * @property-read int|null $promo_codes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromoCode> $usedPromoCodes
 * @property-read int|null $used_promo_codes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMaximumDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMinimumPurchaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereQuantityToGenerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUsageLimitPerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereValidUntil($value)
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $currency_name
 * @property string $code
 * @property string $symbol
 * @property string $thousand_separator
 * @property string $decimal_separator
 * @property int|null $exchange_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCurrencyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereDecimalSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereThousandSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedAt($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property string|null $email
 * @property string $mobile
 * @property string|null $address
 * @property string|null $company_name
 * @property string|null $company_gst_number
 * @property int|null $number_of_children
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\CustomerWaiver|null $waiver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCompanyGstNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNumberOfChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int|null $customer_id
 * @property string $valid_until
 * @property int $photo_video_use_out
 * @property int $agree_to_terms
 * @property string|null $how_did_you_hear_about_us
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereAgreeToTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereHowDidYouHearAboutUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver wherePhotoVideoUseOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerWaiver withoutTrashed()
 */
	class CustomerWaiver extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $rate
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 */
	class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereUpdatedAt($value)
 */
	class EventType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $method_name
 * @property string $short_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereMethodName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereUpdatedAt($value)
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int $product_category_id
 * @property int $product_type 1 = Product, 2 = Service
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string $price
 * @property string|null $cost
 * @property int|null $quantity
 * @property int|null $alert_quantity
 * @property int|null $unit_id
 * @property int|null $tax_id
 * @property int|null $duration_in_minutes
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductCategory $category
 * @property-read float $price_with_tax
 * @property-read float $tax_amount
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Tax|null $tax
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAlertQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDurationInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	class Product extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory withoutTrashed()
 */
	class ProductCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int $coupon_id
 * @property string $code
 * @property int $usage_count
 * @property string $status
 * @property int|null $customer_id
 * @property string|null $first_used_at
 * @property string|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon $coupon
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sale> $sales
 * @property-read int|null $sales_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereFirstUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromoCode whereUsageCount($value)
 */
	class PromoCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $reference
 * @property int $supplier_id
 * @property string $purchase_date
 * @property string $global_discount_amount
 * @property string $line_items_discount_total
 * @property string $total_discount_amount
 * @property string $tax_amount
 * @property string $sub_total
 * @property int|null $tax_id
 * @property string $tax_rate
 * @property int|null $discount_id
 * @property string $discount_rate
 * @property string $shipping_amount
 * @property string|null $note
 * @property string $status Pending, Completed
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Discount|null $discount
 * @property-read mixed $total
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseProduct> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\Tax|null $tax
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereGlobalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereLineItemsDiscountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereShippingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTotalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase withoutTrashed()
 */
	class Purchase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $purchase_id
 * @property int $product_id
 * @property int $quantity
 * @property string $unit_price
 * @property string $discount_amount
 * @property string $tax_rate
 * @property string $tax_amount
 * @property string $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Purchase $purchase
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseProduct whereUpdatedAt($value)
 */
	class PurchaseProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int $quotation_type 1 = Standard, 2 = Event
 * @property string $reference
 * @property int $customer_id
 * @property int|null $event_type_id
 * @property string|null $event_time
 * @property string|null $event_for
 * @property string|null $total_pax
 * @property string $global_discount_amount
 * @property string $line_items_discount_total
 * @property string $total_discount_amount
 * @property string $tax_amount
 * @property string $advance_paid
 * @property string $sub_total
 * @property int|null $tax_id
 * @property string $tax_rate
 * @property int|null $discount_id
 * @property string $discount_rate
 * @property string|null $note
 * @property string $status Pencil Booking, Confirmed, Cancelled, On Hold, Converted
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\EventType|null $eventType
 * @property-read mixed $total
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuotationProduct> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Sale|null $sale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation eventQuotation()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation standardQuotation()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereAdvancePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereEventFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereEventTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereGlobalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereLineItemsDiscountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereQuotationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereTotalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereTotalPax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quotation withoutTrashed()
 */
	class Quotation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $quotation_id
 * @property int $product_id
 * @property int $quantity
 * @property string $unit_price
 * @property string $discount_amount
 * @property string $tax_rate
 * @property string $tax_amount
 * @property string $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Quotation $quotation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereQuotationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuotationProduct whereUpdatedAt($value)
 */
	class QuotationProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int|null $quotation_id
 * @property int $sale_type 1 = Standard, 2 = Event, 3 = POS
 * @property string $reference
 * @property int $customer_id
 * @property int|null $event_type_id
 * @property string|null $event_time
 * @property string|null $event_for
 * @property string|null $total_pax
 * @property string $global_discount_amount
 * @property string $line_items_discount_total
 * @property string $total_discount_amount
 * @property string $tax_amount
 * @property string $sub_total
 * @property int|null $tax_id
 * @property string $tax_rate
 * @property int|null $discount_id
 * @property string $discount_rate
 * @property string|null $note
 * @property string $status Pending, Completed
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Discount|null $discount
 * @property-read \App\Models\EventType|null $eventType
 * @property-read mixed $due
 * @property-read float $net_total
 * @property-read mixed $paid
 * @property-read mixed $total
 * @property-read float $total_returned
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalePayment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SaleProduct> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Quotation|null $quotation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SaleReturn> $saleReturns
 * @property-read int|null $sale_returns_count
 * @property-read \App\Models\Tax|null $tax
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale eventSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale posSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale standardSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereEventFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereEventTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereGlobalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereLineItemsDiscountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereQuotationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereSaleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereTotalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereTotalPax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale withoutTrashed()
 */
	class Sale extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $payment_date
 * @property int $sale_id
 * @property int|null $customer_id
 * @property int|null $created_by
 * @property string $amount
 * @property int|null $payment_method_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \App\Models\Sale $sale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalePayment withoutTrashed()
 */
	class SalePayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sale_id
 * @property int $product_id
 * @property int $quantity
 * @property int $returned_quantity
 * @property string $unit_price
 * @property string $discount_amount
 * @property string $tax_rate
 * @property string $tax_amount
 * @property string $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $net_quantity
 * @property-read mixed $net_sub_total
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Sale $sale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereReturnedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleProduct whereUpdatedAt($value)
 */
	class SaleProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property int $sale_id
 * @property string $reference
 * @property int $customer_id
 * @property string $return_date
 * @property string $global_discount_amount
 * @property string $line_items_discount_total
 * @property string $total_discount_amount
 * @property string $tax_amount
 * @property string $sub_total
 * @property int|null $tax_id
 * @property string $tax_rate
 * @property int|null $discount_id
 * @property string $discount_rate
 * @property string|null $note
 * @property string $status Pending, Completed
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Discount|null $discount
 * @property-read float $due
 * @property-read float $refunded
 * @property-read float $total
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SaleReturnPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SaleReturnProduct> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Sale $sale
 * @property-read \App\Models\Tax|null $tax
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereGlobalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereLineItemsDiscountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereTotalDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturn withoutTrashed()
 */
	class SaleReturn extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $payment_date
 * @property int $sale_return_id
 * @property int|null $customer_id
 * @property int|null $created_by
 * @property string $amount
 * @property int|null $payment_method_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \App\Models\SaleReturn $saleReturn
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereSaleReturnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnPayment withoutTrashed()
 */
	class SaleReturnPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sale_return_id
 * @property int $product_id
 * @property int $quantity
 * @property string $unit_price
 * @property string $discount_amount
 * @property string $tax_rate
 * @property string $tax_amount
 * @property string $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\SaleReturn $saleReturn
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereSaleReturnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleReturnProduct whereUpdatedAt($value)
 */
	class SaleReturnProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $currency_id
 * @property string $company_name
 * @property string|null $company_email
 * @property string|null $company_email_2
 * @property string|null $company_mobile
 * @property string|null $company_mobile_2
 * @property string|null $currency_position
 * @property string|null $notification_email
 * @property string|null $footer_text
 * @property string|null $company_address
 * @property string|null $vat_id
 * @property int|null $tax_percentage
 * @property int $decimal_point
 * @property string|null $date_format
 * @property string|null $time_zone
 * @property string|null $time_format
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency|null $currency
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyEmail2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyMobile2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCurrencyPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDateFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDecimalPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereFooterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereNotificationEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTaxPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTimeFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTimeZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereVatId($value)
 */
	class Setting extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property string|null $email
 * @property string $mobile
 * @property string|null $address
 * @property string|null $company_name
 * @property string|null $company_gst_number
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCompanyGstNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier withoutTrashed()
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $rate
 * @property string|null $cgst
 * @property string|null $sgst
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereCgst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereSgst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tax whereUpdatedAt($value)
 */
	class Tax extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $folder
 * @property string $filename
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upload whereUpdatedAt($value)
 */
	class Upload extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $branch_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $mobile
 * @property string|null $gender Male/Female
 * @property string|null $date_of_birth
 * @property string|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Branch|null $branch
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

