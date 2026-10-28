# Pessimistic row lock for stock reservation

Stock is decremented inside the same transaction that writes the order, and the product
row is read with `SELECT ... FOR UPDATE` before the decrement. Optimistic locking with a
version column and retry was the alternative, but it pushes conflict handling into every
caller and reappears as a retry loop around order creation, which is the path most likely
to grow. Checkout here is short and contended by at most a handful of staff, so holding a
row lock for a few milliseconds is cheaper than a retry policy that every future caller
has to remember.

## Consequences

Two orders touching the same product serialise on that row. Under the current load that
is invisible. If order volume grows past the point where staff block each other, the
first thing to try is shortening the transaction, not switching to optimistic locking:
the lock is held only for the insert and the decrement, so the window is already small.