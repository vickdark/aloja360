# Aloja360 Agent Instructions

Before making changes:

1. Read ALOJA360_BUSINESS.md.
2. Read docs/database-schema.md if it exists.
3. Inspect the existing implementation before creating new abstractions.
4. Do not duplicate business logic.
5. Do not create alternative entities for existing concepts.

## Canonical entities

- Business
- Accommodation
- Amenity
- RatePeriod
- BlockedPeriod
- Guest
- Quote
- Reservation
- ReservationGuest
- ReservationService
- ReservationStatusHistory
- Payment
- Service
- Stay
- CleaningTask
- MaintenanceRequest
- InventoryItem
- InventoryCheck
- InventoryCheckItem
- ExpenseCategory
- Expense
- OutboundMessage
- AuditLog

Do not introduce competing names such as:

- Cabin instead of Accommodation
- Customer instead of Guest
- Booking instead of Reservation

unless explicitly approved.

## Database rules

- Database changes must update docs/database-schema.md when it exists.
- Business-rule changes must update ALOJA360_BUSINESS.md.
- Do not run destructive database commands.
- Never execute:
  - `php artisan migrate:fresh`
  - `php artisan db:wipe`
  without explicit authorization.
- Do not delete existing migrations.

## Architectural rules

- Use Laravel conventions.
- Controllers must remain thin.
- Centralize business logic in Services or Actions.
- Use English names in code and database (models, tables, columns, classes, routes).
- The UI will be Spanish.
- Do not use database ENUM types.
- Use PHP Enums for statuses when appropriate.
- Monetary values must use decimal(14,2), never float.
- Authorization must use Policies where appropriate.
- Database operations involving multiple writes must use transactions.
- Never duplicate availability or pricing logic.

## Core services (to be created)

Availability must ultimately be centralized in:

```text
App\Services\AvailabilityService
```

Pricing logic must be centralized in:

```text
App\Services\PricingService
```

Reservation logic should be centralized in ReservationService or Actions:
- CreateReservationAction
- ConfirmReservationAction
- CancelReservationAction
- CheckInReservationAction
- CheckOutReservationAction

## Availability rules

A reservation overlaps another reservation when:

```text
new_check_in < existing_check_out
AND
new_check_out > existing_check_in
```

Availability must consider both:

```text
reservations + blocked_periods
```

Statuses that block availability:

```text
pending, confirmed, checked_in
```

Statuses that do NOT block:

```text
cancelled, checked_out, no_show
```

A quotation does NOT block availability.

## Reservation statuses

```text
pending
confirmed
checked_in
checked_out
cancelled
no_show
```

## Accommodation statuses

```text
available
reserved
occupied
pending_cleaning
cleaning
maintenance
blocked
```

## Payment statuses

```text
pending
confirmed
rejected
cancelled
```

Payment types:

```text
payment
refund
deposit
deposit_return
```

Payment methods:

```text
cash
bank_transfer
credit_card
debit_card
nequi
daviplata
other
```

## Cleaning statuses

```text
pending
assigned
in_progress
completed
cancelled
```

## Maintenance statuses

```text
reported
scheduled
in_progress
completed
cancelled
```

Maintenance priorities:

```text
low
medium
high
critical
```

## Quote statuses

```text
draft
sent
accepted
rejected
expired
converted
```

## Business flow

```text
Quote
→ Pending Reservation
→ Payment / Deposit
→ Confirmed
→ Check-in
→ Stay
→ Check-out
→ Pending Cleaning
→ Cleaning Completed
→ Available
```

Before converting a quote to a reservation, availability must be checked again inside a transaction.

Check-in should normally only be allowed for confirmed reservations.

Check-out should normally only be allowed for checked-in reservations.

Check-out must create a cleaning task and change the accommodation to pending_cleaning.

Completing cleaning must only change an accommodation to available if no maintenance, block or other operational restriction exists.

## Financial rules

Confirmed payments affect outstanding balance.

Do not use a stored balance as the primary financial truth if it can be calculated from reservation total and confirmed financial transactions.

Outstanding balance formula (conceptual):
```text
reservation.total_amount
- confirmed_payments
+ confirmed_refunds
= outstanding_balance
```

reservation_services must store unit_price and total (historical snapshot).

Do not overwrite historical prices for services or reservations.

Do not delete cancelled reservations. Use status = cancelled.

Any important reservation status change must be stored in reservation_status_histories.

Any important financial modification must be auditable (AuditLog).

## Multi-business security

A user must never be able to access records from another business_id via URL manipulation.

All queries must be scoped by the active user's business.

## After changes

- Run relevant tests.
- Run lint/static checks available in the project.
- Report files changed.
- Report database changes.
- Report business rules implemented.
- Report pending work.
