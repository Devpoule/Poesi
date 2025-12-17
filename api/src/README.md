# src

## Overview
Application source code structured by responsibility:

- **Domain/**: business model and rules
- **Http/**: API delivery layer (controllers, input, output)
- **Infrastructure/**: technical implementations (DB, mail, framework wiring)
- **Support/**: shared generic helpers

## Rule of thumb
Business rules go inward (Domain). Technical details stay outward (Infrastructure).
Http is only a delivery mechanism.
