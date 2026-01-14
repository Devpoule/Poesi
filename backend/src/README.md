# src

## Overview
Application source code structured by responsibility:

- **Domain/**  
  Business model and rules (framework-agnostic)

- **Http/**  
  API delivery layer (controllers, input, output)

- **Infrastructure/**  
  Technical implementations (DB, mail, framework wiring)

- **Command/**  
  CLI commands (sync, maintenance, batch operations)

- **Support/**  
  Shared generic helpers

## Rule of thumb
Business rules go inward (**Domain**).  
Technical details stay outward (**Infrastructure**).  
Http is only a delivery mechanism.  
Commands orchestrate outside HTTP.
