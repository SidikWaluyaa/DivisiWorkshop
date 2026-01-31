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
 * @property int $work_order_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $category
 * @property string $description
 * @property array $photos
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint withoutTrashed()
 */
	class Complaint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cs_lead_id
 * @property int $user_id
 * @property string $type
 * @property string|null $channel
 * @property string $content
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_content
 * @property-read mixed $type_icon
 * @property-read \App\Models\CsLead $lead
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity recent($limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsActivity whereUserId($value)
 */
	class CsActivity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $customer_name
 * @property string $customer_phone
 * @property string|null $customer_email
 * @property string|null $customer_address
 * @property string|null $customer_city
 * @property string|null $customer_province
 * @property string $source
 * @property string|null $source_detail
 * @property \Illuminate\Support\Carbon|null $first_contact_at
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property int|null $response_time_minutes
 * @property string $priority
 * @property numeric|null $expected_value
 * @property string|null $lost_reason
 * @property int|null $converted_to_work_order_id
 * @property string $status
 * @property int $cs_id
 * @property \Illuminate\Support\Carbon $last_activity_at
 * @property \Illuminate\Support\Carbon|null $next_follow_up_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CsActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User $cs
 * @property-read mixed $days_in_stage
 * @property-read mixed $priority_badge_class
 * @property-read mixed $response_time_formatted
 * @property-read mixed $status_badge_class
 * @property-read mixed $wa_greeting_link
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CsQuotation> $quotations
 * @property-read int|null $quotations_count
 * @property-read \App\Models\CsSpk|null $spk
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead bySource($source)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead closing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead converted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead greeting()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead hotLeads()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead konsultasi()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead lost()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereConvertedToWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereExpectedValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereFirstContactAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereFirstResponseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereLostReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereNextFollowUpAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereResponseTimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereSourceDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereUpdatedAt($value)
 */
	class CsLead extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cs_lead_id
 * @property string $quotation_number
 * @property int $version
 * @property array<array-key, mixed> $items
 * @property numeric $subtotal
 * @property numeric $discount
 * @property string $discount_type
 * @property numeric $total
 * @property string|null $notes
 * @property string|null $terms_conditions
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $responded_at
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $valid_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\CsLead $lead
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation accepted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation latestVersion()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation sent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereQuotationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereTermsConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereVersion($value)
 */
	class CsQuotation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cs_lead_id
 * @property int|null $work_order_id
 * @property string $spk_number
 * @property int $customer_id
 * @property array<array-key, mixed> $services
 * @property numeric $total_price
 * @property numeric $dp_amount
 * @property string $dp_status
 * @property \Illuminate\Support\Carbon|null $dp_paid_at
 * @property string|null $payment_method
 * @property string|null $payment_notes
 * @property string|null $proof_image
 * @property \Illuminate\Support\Carbon|null $expected_delivery_date
 * @property string|null $special_instructions
 * @property string|null $shoe_brand
 * @property string|null $shoe_size
 * @property string|null $category
 * @property string|null $shoe_type
 * @property string|null $shoe_color
 * @property string $priority
 * @property string|null $delivery_type
 * @property string|null $cs_code
 * @property string|null $pdf_path
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $handed_at
 * @property int|null $handed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read mixed $dp_percentage
 * @property-read mixed $dp_status_badge_class
 * @property-read mixed $label
 * @property-read mixed $remaining_payment
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User|null $handedByUser
 * @property-read \App\Models\CsLead $lead
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk dpPaid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk readyToHand()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk waitingDp()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereDpAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereDpPaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereDpStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereExpectedDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereHandedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereHandedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk wherePaymentNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereProofImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereShoeBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereShoeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereShoeSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereShoeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereSpecialInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereWorkOrderId($value)
 */
	class CsSpk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $city
 * @property string|null $city_id
 * @property string|null $province
 * @property string|null $province_id
 * @property string|null $postal_code
 * @property string|null $district
 * @property string|null $village
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $workOrders
 * @property-read int|null $work_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereVillage($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $file_path
 * @property string|null $caption
 * @property string $type
 * @property int|null $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\User|null $uploader
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerPhoto whereUploadedBy($value)
 */
	class CustomerPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property int $reported_by
 * @property string $type
 * @property string|null $category
 * @property string|null $description
 * @property array<array-key, mixed>|null $photos
 * @property string $status
 * @property string|null $resolution
 * @property string|null $resolution_notes
 * @property int|null $resolved_by
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User|null $resolver
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereWorkOrderId($value)
 */
	class CxIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $sub_category
 * @property string|null $size
 * @property int $stock
 * @property int $reserved_stock
 * @property string $unit
 * @property numeric $price
 * @property int $min_stock
 * @property string $status
 * @property int|null $pic_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $pic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereMinStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material wherePicUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereReservedStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereSubCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUpdatedAt($value)
 */
	class Material extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $material_id
 * @property int|null $oto_id
 * @property int|null $work_order_id
 * @property int $quantity
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $released_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Material $material
 * @property-read \App\Models\OTO|null $oto
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation hard()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation soft()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereOtoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereReleasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialReservation whereWorkOrderId($value)
 */
	class MaterialReservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string|null $title
 * @property string|null $description
 * @property string $oto_type
 * @property array<array-key, mixed> $proposed_services
 * @property numeric $total_normal_price
 * @property numeric $total_oto_price
 * @property numeric $total_discount
 * @property numeric $discount_percent
 * @property int $estimated_days
 * @property \Illuminate\Support\Carbon $valid_until
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $customer_responded_at
 * @property string|null $customer_note
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int $priority_score
 * @property bool $is_fast_track
 * @property numeric $dp_required
 * @property bool $dp_paid
 * @property \Illuminate\Support\Carbon|null $dp_paid_at
 * @property bool $materials_reserved
 * @property bool $materials_confirmed
 * @property int|null $created_by
 * @property int|null $cx_assigned_to
 * @property \Illuminate\Support\Carbon|null $cx_contacted_at
 * @property string|null $cx_contact_method
 * @property string|null $cx_notes
 * @property int $cx_follow_up_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OTOContactLog> $contactLogs
 * @property-read int|null $contact_logs_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $cxAssigned
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialReservation> $materialReservations
 * @property-read int|null $material_reservations_count
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO pendingCustomer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxContactMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxContactedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxFollowUpCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDpPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDpPaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDpRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereEstimatedDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereIsFastTrack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereMaterialsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereMaterialsReserved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereOtoType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO wherePriorityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereProposedServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalNormalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalOtoPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereWorkOrderId($value)
 */
	class OTO extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $oto_id
 * @property int $contacted_by
 * @property string $contact_method
 * @property string|null $notes
 * @property string|null $customer_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $contactedBy
 * @property-read \App\Models\OTO $oto
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereContactMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereContactedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereCustomerResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereOtoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTOContactLog whereUpdatedAt($value)
 */
	class OTOContactLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string $type
 * @property int $amount_total
 * @property int $amount_service
 * @property int $amount_shipping
 * @property string $payment_method
 * @property \Illuminate\Support\Carbon $paid_at
 * @property int $pic_id
 * @property string|null $notes
 * @property string|null $proof_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $pic
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereProofImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereWorkOrderId($value)
 */
	class OrderPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $po_number
 * @property string|null $supplier_name
 * @property int $material_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $total_price
 * @property numeric $paid_amount
 * @property string $status
 * @property string $payment_status
 * @property int|null $quality_rating 1-5 rating
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $received_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $outstanding_amount
 * @property-read \App\Models\Material $material
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase wherePoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereQualityRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUpdatedAt($value)
 */
	class Purchase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $category
 * @property numeric $price
 * @property int $duration_minutes
 * @property string $unit
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withoutTrashed()
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string $rack_code
 * @property string $item_type
 * @property \Illuminate\Support\Carbon $stored_at
 * @property \Illuminate\Support\Carbon|null $retrieved_at
 * @property int|null $stored_by
 * @property int|null $retrieved_by
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StorageRack $rack
 * @property-read \App\Models\User|null $retrievedByUser
 * @property-read \App\Models\User|null $storedByUser
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment accessories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment byRack(string $rackCode)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment isRetrieved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment overdue(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment shoes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment stored()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereRackCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereRetrievedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereRetrievedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereStoredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereStoredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereWorkOrderId($value)
 */
	class StorageAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $rack_code
 * @property string $category
 * @property string|null $location
 * @property int $capacity
 * @property int $current_count
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StorageAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $storedOrders
 * @property-read int|null $stored_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack accessories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack byLocation(string $location)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack orderByUtilization(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack shoes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCurrentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereRackCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereUpdatedAt($value)
 */
	class StorageRack extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $cs_code 3 letter code for CS/SPK
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property array<array-key, mixed>|null $access_rights
 * @property string|null $specialization
 * @property string|null $phone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsPrepSol
 * @property-read int|null $jobs_prep_sol_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsPrepUpper
 * @property-read int|null $jobs_prep_upper_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsPrepWashing
 * @property-read int|null $jobs_prep_washing_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsProdCleaning
 * @property-read int|null $jobs_prod_cleaning_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsProdSol
 * @property-read int|null $jobs_prod_sol_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsProdUpper
 * @property-read int|null $jobs_prod_upper_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsProduction
 * @property-read int|null $jobs_production_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsQcCleanup
 * @property-read int|null $jobs_qc_cleanup_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsQcFinal
 * @property-read int|null $jobs_qc_final_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsQcJahit
 * @property-read int|null $jobs_qc_jahit_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsSortirSol
 * @property-read int|null $jobs_sortir_sol_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $jobsSortirUpper
 * @property-read int|null $jobs_sortir_upper_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $qcFinalCompleted
 * @property-read int|null $qc_final_completed_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAccessRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSpecialization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $spk_number
 * @property string|null $cs_code CS Code from SPK Number
 * @property string $customer_name
 * @property string $customer_phone
 * @property string|null $customer_address
 * @property string|null $shoe_brand
 * @property string|null $shoe_type
 * @property string|null $shoe_size
 * @property string|null $shoe_color
 * @property string|null $category Sepatu, Tas, Topi, Lainnya
 * @property \App\Enums\WorkOrderStatus $status
 * @property string|null $payment_proof
 * @property string|null $payment_method
 * @property string|null $payment_notes
 * @property int $reminder_count
 * @property \Illuminate\Support\Carbon|null $last_reminder_at
 * @property \Illuminate\Support\Carbon|null $donated_at
 * @property numeric|null $priority_score
 * @property int $has_active_oto
 * @property numeric $oto_addition_amount
 * @property numeric $oto_discount_amount
 * @property int $oto_priority_boost
 * @property numeric $discount
 * @property int|null $unique_code
 * @property string|null $previous_status
 * @property int $total_service_price
 * @property int $cost_oto
 * @property int $cost_add_service
 * @property int $shipping_cost
 * @property string|null $shipping_type
 * @property string|null $shipping_zone
 * @property float $total_transaksi
 * @property float $total_paid
 * @property float $sisa_tagihan
 * @property string|null $status_pembayaran
 * @property \Illuminate\Support\Carbon|null $payment_due_date
 * @property string|null $category_spk
 * @property string|null $payment_status_detail
 * @property string|null $final_status
 * @property array<array-key, mixed>|null $accessories_data
 * @property bool|null $reception_qc_passed
 * @property string|null $reception_rejection_reason
 * @property string|null $notes
 * @property string|null $technician_notes
 * @property string $priority
 * @property string|null $transaction_type
 * @property string|null $source_jasa
 * @property string|null $current_location
 * @property int $is_revising
 * @property \Illuminate\Support\Carbon $entry_date
 * @property \Illuminate\Support\Carbon|null $taken_date
 * @property \Illuminate\Support\Carbon|null $estimation_date
 * @property \Illuminate\Support\Carbon|null $finished_date
 * @property \Illuminate\Support\Carbon|null $prep_washing_started_at
 * @property \Illuminate\Support\Carbon|null $prep_washing_completed_at
 * @property int|null $prep_washing_by
 * @property \Illuminate\Support\Carbon|null $prep_sol_started_at
 * @property \Illuminate\Support\Carbon|null $prep_sol_completed_at
 * @property int|null $prep_sol_by
 * @property \Illuminate\Support\Carbon|null $prep_upper_started_at
 * @property \Illuminate\Support\Carbon|null $prep_upper_completed_at
 * @property int|null $prep_upper_by
 * @property \Illuminate\Support\Carbon|null $prod_sol_started_at
 * @property \Illuminate\Support\Carbon|null $prod_sol_completed_at
 * @property int|null $prod_sol_by
 * @property \Illuminate\Support\Carbon|null $prod_upper_started_at
 * @property \Illuminate\Support\Carbon|null $prod_upper_completed_at
 * @property int|null $prod_upper_by
 * @property \Illuminate\Support\Carbon|null $prod_cleaning_started_at
 * @property \Illuminate\Support\Carbon|null $prod_cleaning_completed_at
 * @property int|null $prod_cleaning_by
 * @property \Illuminate\Support\Carbon|null $qc_jahit_started_at
 * @property \Illuminate\Support\Carbon|null $qc_jahit_completed_at
 * @property int|null $qc_jahit_by
 * @property \Illuminate\Support\Carbon|null $qc_cleanup_started_at
 * @property \Illuminate\Support\Carbon|null $qc_cleanup_completed_at
 * @property int|null $qc_cleanup_by
 * @property \Illuminate\Support\Carbon|null $qc_final_started_at
 * @property \Illuminate\Support\Carbon|null $qc_final_completed_at
 * @property string|null $storage_rack_code
 * @property string|null $stored_at
 * @property string|null $retrieved_at
 * @property int|null $qc_final_by
 * @property int|null $technician_production_id
 * @property int|null $pic_sortir_sol_id
 * @property int|null $pic_sortir_upper_id
 * @property int|null $qc_jahit_technician_id
 * @property int|null $qc_cleanup_technician_id
 * @property int|null $qc_final_pic_id
 * @property int|null $created_by
 * @property string|null $customer_email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $finance_entry_at
 * @property \Illuminate\Support\Carbon|null $finance_exit_at
 * @property int|null $pic_finance_id
 * @property string|null $accessories_tali Simpan/Nempel/Tidak Ada
 * @property string|null $accessories_insole
 * @property string|null $accessories_box
 * @property string|null $accessories_other
 * @property string|null $warehouse_qc_status
 * @property string|null $warehouse_qc_notes
 * @property int|null $warehouse_qc_by
 * @property \Illuminate\Support\Carbon|null $warehouse_qc_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Complaint> $complaints
 * @property-read int|null $complaints_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CxIssue> $cxIssues
 * @property-read int|null $cx_issues_count
 * @property-read mixed $days_remaining
 * @property-read mixed $is_overdue
 * @property-read bool $is_production_finished
 * @property-read bool $is_qc_finished
 * @property-read bool $is_ready
 * @property-read bool $is_sortir_finished
 * @property-read bool $needs_sol
 * @property-read bool $needs_upper
 * @property-read mixed $spk_cover_photo
 * @property-read mixed $total_price
 * @property-read mixed $urgency_level
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Material> $materials
 * @property-read int|null $materials_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read \App\Models\User|null $picFinance
 * @property-read \App\Models\User|null $picSortirSol
 * @property-read \App\Models\User|null $picSortirUpper
 * @property-read \App\Models\User|null $prepSolBy
 * @property-read \App\Models\User|null $prepUpperBy
 * @property-read \App\Models\User|null $prepWashingBy
 * @property-read \App\Models\User|null $prodCleaningBy
 * @property-read \App\Models\User|null $prodSolBy
 * @property-read \App\Models\User|null $prodUpperBy
 * @property-read \App\Models\User|null $qcCleanupBy
 * @property-read \App\Models\User|null $qcCleanupTechnician
 * @property-read \App\Models\User|null $qcFinalBy
 * @property-read \App\Models\User|null $qcFinalPic
 * @property-read \App\Models\User|null $qcJahitBy
 * @property-read \App\Models\User|null $qcJahitTechnician
 * @property-read \App\Models\WorkOrderService|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StorageAssignment> $storageAssignments
 * @property-read int|null $storage_assignments_count
 * @property-read \App\Models\User|null $technicianProduction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderService> $workOrderServices
 * @property-read int|null $work_order_services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereAccessoriesBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereAccessoriesData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereAccessoriesInsole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereAccessoriesOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereAccessoriesTali($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCategorySpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCostAddService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCostOto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCurrentLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDonatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereEstimationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinanceEntryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinanceExitAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinishedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereHasActiveOto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereIsRevising($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereLastReminderAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereOtoAdditionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereOtoDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereOtoPriorityBoost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePaymentDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePaymentNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePaymentStatusDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePicFinanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePicSortirSolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePicSortirUpperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepSolBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepSolCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepSolStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepUpperBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepUpperCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepUpperStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepWashingBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepWashingCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePrepWashingStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePreviousStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePriorityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdCleaningBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdCleaningCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdCleaningStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdSolBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdSolCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdSolStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdUpperBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdUpperCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereProdUpperStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcCleanupBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcCleanupCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcCleanupStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcCleanupTechnicianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcFinalBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcFinalCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcFinalPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcFinalStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcJahitBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcJahitCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcJahitStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereQcJahitTechnicianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereReceptionQcPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereReceptionRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereReminderCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereRetrievedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShippingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShippingZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShoeBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShoeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShoeSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereShoeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereSisaTagihan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereSourceJasa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStatusPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStorageRackCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStoredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTakenDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTechnicianNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTechnicianProductionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTotalPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTotalServicePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTotalTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereUniqueCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder withoutTrashed()
 */
	class WorkOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property int|null $user_id
 * @property string|null $step
 * @property string $action
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereWorkOrderId($value)
 */
	class WorkOrderLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string $step
 * @property string $file_path
 * @property bool $is_spk_cover
 * @property string|null $caption
 * @property bool $is_public
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $uploader
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereIsSpkCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereWorkOrderId($value)
 */
	class WorkOrderPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property int|null $service_id
 * @property string|null $custom_service_name
 * @property string|null $category_name
 * @property numeric $cost
 * @property array<array-key, mixed>|null $service_details
 * @property string $status
 * @property string|null $custom_name
 * @property int|null $technician_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service|null $service
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereCustomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereCustomServiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereServiceDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereTechnicianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereWorkOrderId($value)
 */
	class WorkOrderService extends \Eloquent {}
}

