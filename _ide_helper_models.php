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
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string|null $invoice_number
 * @property numeric $amount
 * @property string|null $description
 * @property string|null $bank_code
 * @property string $mutation_type
 * @property bool $used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PaymentVerification|null $verification
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation creditsOnly()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation unused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereBankCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereMutationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankMutation whereUsed($value)
 */
	class BankMutation extends \Eloquent {}
}

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
 * @property-read mixed $photo_urls
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
 * @property int|null $user_id
 * @property string $type
 * @property string|null $channel
 * @property string $content
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_content
 * @property-read mixed $type_icon
 * @property-read \App\Models\CsLead $lead
 * @property-read \App\Models\User|null $user
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
 * @property string $channel
 * @property string $customer_phone
 * @property string|null $customer_email
 * @property string|null $customer_address
 * @property string|null $customer_city
 * @property string|null $customer_province
 * @property string|null $source
 * @property string|null $source_detail
 * @property \Illuminate\Support\Carbon|null $first_contact_at
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property int|null $response_time_minutes
 * @property string|null $priority
 * @property numeric|null $expected_value
 * @property string|null $lost_reason
 * @property int|null $converted_to_work_order_id
 * @property string $status
 * @property int|null $cs_id
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $next_follow_up_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CsActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $cs
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead followUp()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead greeting()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead hotLeads()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead konsultasi()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead lost()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereConvertedToWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereCustomerProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsLead withoutTrashed()
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
 * @property string|null $shoe_brand
 * @property string|null $shoe_type
 * @property string|null $shoe_color
 * @property string|null $shoe_size
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\CsLead $lead
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CsQuotationItem> $quotationItems
 * @property-read int|null $quotation_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation accepted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation latestVersion()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation sent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereQuotationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereShoeBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereShoeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereShoeSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereShoeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereTermsConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotation withoutTrashed()
 */
	class CsQuotation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $quotation_id
 * @property int $item_number
 * @property string|null $category
 * @property string|null $shoe_type
 * @property string|null $shoe_brand
 * @property string|null $shoe_size
 * @property string|null $shoe_color
 * @property string|null $photo_path
 * @property string|null $condition_notes
 * @property array<array-key, mixed>|null $services
 * @property numeric $item_total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $item_notes
 * @property-read string $category_icon
 * @property-read string $label
 * @property-read mixed $photo_url
 * @property-read \App\Models\CsQuotation $quotation
 * @property-read \App\Models\CsSpkItem|null $spkItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereConditionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereItemNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereItemNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereItemTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereQuotationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereShoeBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereShoeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereShoeSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereShoeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsQuotationItem whereUpdatedAt($value)
 */
	class CsQuotationItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cs_lead_id
 * @property int|null $work_order_id
 * @property string $spk_number
 * @property int $total_items
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read mixed $dp_percentage
 * @property-read mixed $dp_status_badge_class
 * @property-read mixed $label
 * @property-read mixed $remaining_payment
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User|null $handedByUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CsSpkItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\CsLead $lead
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk dpPaid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk readyToHand()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk waitingDp()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereTotalItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk whereWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpk withoutTrashed()
 */
	class CsSpk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $spk_id
 * @property int|null $quotation_item_id
 * @property int|null $work_order_id
 * @property string|null $category
 * @property string|null $shoe_type
 * @property string|null $shoe_brand
 * @property string|null $shoe_size
 * @property string|null $shoe_color
 * @property array<array-key, mixed>|null $services
 * @property numeric $item_total_price
 * @property int|null $promotion_id
 * @property numeric|null $original_price Price before discount
 * @property numeric $discount_amount Discount amount from promo
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $item_notes
 * @property-read string $category_icon
 * @property-read string $category_prefix
 * @property-read float $final_price
 * @property-read string $label
 * @property-read string $status_badge_class
 * @property-read \App\Models\Promotion|null $promotion
 * @property-read \App\Models\CsQuotationItem|null $quotationItem
 * @property-read \App\Models\CsSpk $spk
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereItemNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereItemTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereQuotationItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereShoeBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereShoeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereShoeSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereShoeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereSpkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CsSpkItem whereWorkOrderId($value)
 */
	class CsSpkItem extends \Eloquent {}
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
 * @property string|null $district_id
 * @property string|null $village_id
 * @property string|null $province
 * @property string|null $province_id
 * @property string|null $postal_code
 * @property string|null $district
 * @property string|null $village
 * @property string|null $notes
 * @property string|null $address_token
 * @property string|null $address_verification_url
 * @property \Illuminate\Support\Carbon|null $address_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $is_address_verified
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $workOrders
 * @property-read int|null $work_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddressToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddressVerificationUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddressVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDistrictId($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereVillageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
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
 * @property-read mixed $photo_url
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
 * @property string|null $spk_number
 * @property string|null $customer_phone
 * @property string|null $customer_name
 * @property int|null $reported_by
 * @property string $type
 * @property string $source
 * @property string|null $category
 * @property string|null $description
 * @property string|null $kendala
 * @property string|null $opsi_solusi
 * @property string|null $kendala_1
 * @property string|null $kendala_2
 * @property string|null $opsi_solusi_1
 * @property string|null $opsi_solusi_2
 * @property string|null $desc_upper
 * @property string|null $desc_sol
 * @property string|null $desc_kondisi_bawaan
 * @property string|null $suggested_services
 * @property string|null $recommended_services
 * @property string|null $rec_service_1
 * @property string|null $rec_service_2
 * @property string|null $sug_service_1
 * @property string|null $sug_service_2
 * @property array<array-key, mixed>|null $photos
 * @property string $status
 * @property string $shipping_status
 * @property string|null $resolution
 * @property string|null $resolution_notes
 * @property int|null $resolved_by
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $photo_urls
 * @property-read \App\Models\User|null $reporter
 * @property-read \App\Models\User|null $resolver
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereDescKondisiBawaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereDescSol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereDescUpper($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereKendala($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereKendala1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereKendala2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereOpsiSolusi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereOpsiSolusi1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereOpsiSolusi2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereRecService1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereRecService2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereRecommendedServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereReportedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereShippingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereSugService1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereSugService2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereSuggestedServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CxIssue whereWorkOrderId($value)
 */
	class CxIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $invoice_number
 * @property int $customer_id
 * @property numeric $total_amount
 * @property numeric $paid_amount
 * @property numeric $discount
 * @property numeric $shipping_cost
 * @property string $status
 * @property string $spk_status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $estimasi_selesai
 * @property string|null $notes
 * @property string|null $invoice_awal_url
 * @property string|null $invoice_akhir_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read string $payment_status_code
 * @property-read mixed $remaining_balance
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePayment> $invoicePayments
 * @property-read int|null $invoice_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $workOrders
 * @property-read int|null $work_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereEstimasiSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceAkhirUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceAwalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSpkStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $payment_date
 * @property string|null $notes
 * @property bool $verified
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read bool $is_verified
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\PaymentVerification|null $verification
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment unverified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereVerified($value)
 */
	class InvoicePayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $item_name
 * @property string|null $spk_number
 * @property string $rack_code
 * @property int $quantity
 * @property string|null $image_path
 * @property string|null $description
 * @property string $status
 * @property string $payment_status
 * @property numeric $total_price
 * @property numeric $paid_amount
 * @property \Illuminate\Support\Carbon $in_date
 * @property \Illuminate\Support\Carbon|null $out_date
 * @property int $stored_by
 * @property int|null $retrieved_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read \App\Models\StorageRack|null $rack
 * @property-read \App\Models\User|null $retriever
 * @property-read \App\Models\User $storer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereRackCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereRetrievedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereStoredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ManualStorageItem withoutTrashed()
 */
	class ManualStorageItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $category
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterIssue whereUpdatedAt($value)
 */
	class MasterIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $category
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterSolution whereUpdatedAt($value)
 */
	class MasterSolution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $category Material category: SHOPPING (no stock check) or PRODUCTION (stock-dependent)
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialRequestItem> $materialRequests
 * @property-read int|null $material_requests_count
 * @property-read \App\Models\User|null $pic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialReservation> $reservations
 * @property-read int|null $reservations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material withoutTrashed()
 */
	class Material extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $request_number Format: REQ-YYYY-0001
 * @property int|null $work_order_id
 * @property int|null $oto_id
 * @property int $requested_by
 * @property string $type SHOPPING: budget request, PRODUCTION_PO: purchase order
 * @property string $status
 * @property string|null $notes
 * @property numeric $total_estimated_cost
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialRequestItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\OTO|null $oto
 * @property-read \App\Models\User $requestedBy
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest productionPO()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest shopping()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereOtoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereRequestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereTotalEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest whereWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequest withoutTrashed()
 */
	class MaterialRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $material_request_id
 * @property int|null $material_id
 * @property string $material_name
 * @property string|null $specification Size, sub-category, etc.
 * @property int $quantity
 * @property string $unit
 * @property numeric $estimated_price
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Material|null $material
 * @property-read \App\Models\MaterialRequest $materialRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereEstimatedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereMaterialName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereMaterialRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereSpecification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialRequestItem whereUpdatedAt($value)
 */
	class MaterialRequestItem extends \Eloquent {}
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
 * @property string|null $spk_number
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $title
 * @property string|null $description
 * @property string $oto_type
 * @property string $proposed_services
 * @property string $total_normal_price
 * @property string $total_oto_price
 * @property string $total_discount
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
 * @property string $dp_required
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO pendingCustomer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCustomerRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxContactMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxContactedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxFollowUpCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereCxNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalNormalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereTotalOtoPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO whereWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OTO withoutTrashed()
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
 * @property int|null $work_order_id
 * @property string|null $spk_number_snapshot
 * @property string $type
 * @property int $amount_total
 * @property int $amount_service
 * @property int $amount_shipping
 * @property string $payment_method
 * @property \Illuminate\Support\Carbon $paid_at
 * @property int|null $pic_id
 * @property string|null $notes
 * @property string|null $proof_image
 * @property string|null $services_snapshot
 * @property string|null $customer_name_snapshot
 * @property string|null $customer_phone_snapshot
 * @property int|null $total_bill_snapshot
 * @property int|null $discount_snapshot
 * @property int|null $shipping_cost_snapshot
 * @property int|null $balance_snapshot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $invoice_id
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\User|null $pic
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereAmountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereBalanceSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereCustomerNameSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereCustomerPhoneSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereDiscountSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereProofImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereServicesSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereShippingCostSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereSpkNumberSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereTotalBillSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereWorkOrderId($value)
 */
	class OrderPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $payment_id
 * @property int $mutation_id
 * @property int $verified_by
 * @property \Illuminate\Support\Carbon $verified_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankMutation $mutation
 * @property-read \App\Models\InvoicePayment $payment
 * @property-read \App\Models\User $verifier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereMutationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentVerification whereVerifiedBy($value)
 */
	class PaymentVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property numeric|null $discount_percentage For PERCENTAGE type (e.g., 20.00 = 20%)
 * @property numeric|null $discount_amount For FIXED type (e.g., 50000)
 * @property numeric|null $max_discount_amount Maximum discount cap
 * @property numeric|null $min_purchase_amount Minimum purchase requirement
 * @property \Illuminate\Support\Carbon $valid_from
 * @property \Illuminate\Support\Carbon $valid_until
 * @property bool $is_active
 * @property string $applicable_to
 * @property string $customer_tier
 * @property int|null $max_usage_total Total maximum usage across all customers
 * @property int $max_usage_per_customer Maximum usage per customer
 * @property int $current_usage_count Current usage counter
 * @property bool $is_stackable Can be stacked with other promos
 * @property int $priority Higher number = higher priority
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromotionBundle> $bundles
 * @property-read int|null $bundles_count
 * @property-read \App\Models\User|null $creator
 * @property-read string $badge_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromotionUsageLog> $usageLogs
 * @property-read int|null $usage_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion forService(int $serviceId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereApplicableTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCurrentUsageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCustomerTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereIsStackable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereMaxDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereMaxUsagePerCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereMaxUsageTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereMinPurchaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion withoutTrashed()
 */
	class Promotion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $promotion_id
 * @property int|null $user_id
 * @property string $type
 * @property string $content
 * @property array<array-key, mixed>|null $metadata JSON
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Promotion $promotion
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionActivity whereUserId($value)
 */
	class PromotionActivity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $promotion_id
 * @property array<array-key, mixed> $required_services Array of service IDs that must be selected together (JSON)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Promotion $promotion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle whereRequiredServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionBundle whereUpdatedAt($value)
 */
	class PromotionBundle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $promotion_id
 * @property int|null $cs_lead_id
 * @property int|null $cs_spk_id
 * @property int|null $work_order_id
 * @property string|null $customer_phone
 * @property numeric $original_amount Price before discount
 * @property numeric $discount_amount Discount amount
 * @property numeric $final_amount Price after discount
 * @property int|null $applied_by User ID who applied the promo
 * @property \Illuminate\Support\Carbon $applied_at
 * @property-read \App\Models\User|null $appliedBy
 * @property-read \App\Models\CsLead|null $csLead
 * @property-read \App\Models\Promotion $promotion
 * @property-read \App\Models\WorkOrder|null $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereAppliedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereCsLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereCsSpkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereFinalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereOriginalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionUsageLog whereWorkOrderId($value)
 */
	class PromotionUsageLog extends \Eloquent {}
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $outstanding_amount
 * @property-read \App\Models\Material $material
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDeletedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase withoutTrashed()
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
 * @property \Illuminate\Support\Carbon $tanggal_masuk
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $spk_number
 * @property bool $is_verified
 * @property string|null $kategori_pengiriman
 * @property \Illuminate\Support\Carbon|null $tanggal_pengiriman
 * @property string|null $pic
 * @property string|null $resi_pengiriman
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereKategoriPengiriman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping wherePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereResiPengiriman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereSpkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereTanggalMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereTanggalPengiriman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipping whereWorkOrderId($value)
 */
	class Shipping extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string $rack_code
 * @property string|null $category
 * @property string|null $item_type
 * @property \Illuminate\Support\Carbon $stored_at
 * @property \Illuminate\Support\Carbon|null $retrieved_at
 * @property int|null $stored_by
 * @property int|null $retrieved_by
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StorageRack|null $rack
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageAssignment whereCategory($value)
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
 * @property \App\Enums\StorageCategory|null $category
 * @property string|null $location
 * @property int $capacity
 * @property int $current_count
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StorageAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $storedOrders
 * @property-read int|null $stored_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack accessories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack before()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack byLocation(string $location)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack manual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack orderByUtilization(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack shoes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereCurrentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereRackCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StorageRack withoutTrashed()
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
 * @property int|null $invoice_id
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
 * @property string|null $finish_report_url
 * @property \Illuminate\Support\Carbon|null $waktu Waktu perubahan status terakhir
 * @property int|null $workshop_manifest_id
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
 * @property \App\Enums\WorkOrderStatus|null $previous_status
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
 * @property string|null $invoice_token
 * @property string|null $invoice_awal
 * @property string|null $invoice_akhir
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
 * @property \Illuminate\Support\Carbon|null $new_estimation_date
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
 * @property int|null $cx_handler_id
 * @property string|null $accessories_tali Simpan/Nempel/Tidak Ada
 * @property string|null $accessories_insole
 * @property string|null $accessories_box
 * @property string|null $accessories_other
 * @property string|null $warehouse_qc_status
 * @property string|null $warehouse_qc_notes
 * @property int|null $warehouse_qc_by
 * @property \Illuminate\Support\Carbon|null $warehouse_qc_at
 * @property string|null $late_description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Complaint> $complaints
 * @property-read int|null $complaints_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\User|null $cxHandler
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
 * @property-read mixed $spk_cover_photo_url
 * @property-read mixed $total_price
 * @property-read mixed $urgency_level
 * @property-read \App\Models\Invoice|null $invoice
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
 * @property-read \App\Models\Shipping|null $shipping
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StorageAssignment> $storageAssignments
 * @property-read int|null $storage_assignments_count
 * @property-read \App\Models\User|null $technicianProduction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderPhoto> $warehouseBeforePhotos
 * @property-read int|null $warehouse_before_photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderService> $workOrderServices
 * @property-read int|null $work_order_services_count
 * @property-read \App\Models\WorkshopManifest|null $workshopManifest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder productionLate()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder productionSol()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder productionTreatment()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder productionUpper()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder qcCleanup()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder qcFinal()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder qcJahit()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder qcReview()
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCxHandlerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereDonatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereEstimationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinanceEntryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinanceExitAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinishReportUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereFinishedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereHasActiveOto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereInvoiceAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereInvoiceAwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereInvoiceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereIsRevising($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereLastReminderAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereLateDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereNewEstimationDate($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWarehouseQcStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWorkshopManifestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder withServiceCategory($categoryNames)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder withoutServiceCategory($categoryNames)
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
 * @property bool $is_primary_reference
 * @property string|null $caption
 * @property bool $is_public
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $photo_url
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderPhoto whereIsPrimaryReference($value)
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
 * @property int|null $promotion_id
 * @property numeric|null $original_cost Cost before discount
 * @property numeric $discount_amount Discount amount from promo
 * @property array<array-key, mixed>|null $service_details
 * @property string $status
 * @property string|null $notes
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereOriginalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereServiceDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereTechnicianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderService whereWorkOrderId($value)
 */
	class WorkOrderService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $manifest_number
 * @property int|null $dispatcher_id
 * @property int|null $receiver_id
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $dispatched_at
 * @property \Illuminate\Support\Carbon|null $received_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $dispatcher
 * @property-read \App\Models\User|null $receiver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $workOrders
 * @property-read int|null $work_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereDispatchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereDispatcherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereManifestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopManifest withoutTrashed()
 */
	class WorkshopManifest extends \Eloquent {}
}

