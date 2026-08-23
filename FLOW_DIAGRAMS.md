# FlowHub Sequence Diagrams 🗺️

This document contains the technical sequence diagrams for all core application flows.
> **Note on VS Code:** By default, VS Code displays Markdown files as raw text. To view these Mermaid diagrams correctly, you need to install the **"Markdown Preview Mermaid Support"** extension and open the Markdown Preview (Ctrl+Shift+V or by clicking the split-preview icon in the top right corner).

---

## 1. Authentication Flow (Login & Register)
```mermaid
sequenceDiagram
    autonumber
    actor User
    participant View as login.blade.php
    participant Route as routes/auth.php
    participant Controller as AuthenticatedSessionController
    participant DB as Database (users)

    User->>View: Enters email and password
    View->>Route: POST /login
    Route->>Controller: store(LoginRequest $request)
    Controller->>Controller: Validates credentials
    Controller->>DB: Auth::attempt()
    DB-->>Controller: Confirms user exists and password matches
    Controller->>Controller: Regenerates session (Prevents Fixation)
    Controller-->>User: Redirects to /home (Dashboard)
```

---

## 2. OAuth Connection Flow (Google/GitHub)
```mermaid
sequenceDiagram
    autonumber
    actor User
    participant View as connections/index.blade.php
    participant Route as routes/web.php
    participant Controller as SocialAuthController
    participant Socialite as Laravel Socialite
    participant API as OAuth Server (Google/GitHub)
    participant DB as Database (service_connections)

    User->>View: Clicks "Connect Google"
    View->>Route: GET /auth/google/redirect
    Route->>Controller: redirect($provider)
    Controller->>Socialite: driver('google')->redirect()
    Socialite-->>User: Redirects to Google consent screen
    User->>API: Logs in and grants permissions
    API-->>Route: Redirects to GET /auth/google/callback with ?code=...
    Route->>Controller: callback($provider)
    Controller->>Socialite: driver('google')->user()
    Socialite->>API: Exchanges code for Access Token
    API-->>Socialite: Returns Access Token, Refresh Token, Expires_in
    Socialite-->>Controller: Returns $socialUser object
    Controller->>DB: updateOrCreate(ServiceConnection)
    DB-->>Controller: Saves encrypted credentials in DB
    Controller-->>User: Redirects to /connections with success message
```

---

## 3. Automation Management Flow (CRUD)
```mermaid
sequenceDiagram
    autonumber
    actor User
    participant View as automations/create.blade.php
    participant Route as routes/web.php
    participant Controller as AutomationController
    participant Request as StoreAutomationRequest
    participant DB as Database (Multiple Tables)

    User->>View: Configures Trigger, Conditions, and Actions
    View->>Route: POST /automations
    Route->>Request: Validates incoming JSON
    Request-->>Controller: Passes strict array validation
    Controller->>DB: Starts DB Transaction
    Controller->>DB: Creates Automation (Parent record)
    Controller->>DB: Creates AutomationTrigger
    loop For each Condition
        Controller->>DB: Creates AutomationCondition
    end
    loop For each Action
        Controller->>DB: Creates AutomationAction
    end
    Controller->>DB: Commits DB Transaction
    Controller-->>User: Redirects to automations list with success message
```

---

## 4. Core Execution Flow (Webhook -> Worker -> API)
```mermaid
sequenceDiagram
    autonumber
    actor GitHub as GitHub (Webhook POST)
    participant Route as routes/web.php
    participant Controller as GitHubWebhookController
    participant Engine as ExecutionEngine
    participant DB as Database
    participant Redis as Redis Queue (RAM)
    participant Worker as Queue Worker (Background)
    participant Job as ProcessAutomationAction
    participant Adapter as ProviderAdapter
    actor API as External API (Google/GitHub)

    %% Producer Phase
    Note over GitHub, Redis: SYNCHRONOUS PHASE (PRODUCER)
    GitHub->>Route: Sends Event Payload
    Route->>Controller: handle()
    Controller->>Controller: Verifies HMAC Signature (Security)
    Controller->>Engine: process(payload)
    Engine->>DB: Finds matching active automations
    Engine->>DB: Creates ExecutionStep (Status: PENDING)
    Engine->>Redis: dispatch(Job with Step ID)
    Controller-->>GitHub: HTTP 200 OK (Received)

    %% Consumer Phase
    Note over Worker, API: ASYNCHRONOUS PHASE (CONSUMER)
    Worker->>Redis: Polls for pending jobs
    Redis-->>Worker: Returns Job JSON
    Worker->>Job: Executes handle()
    Job->>DB: Updates ExecutionStep to PROCESSING
    Job->>DB: Retrieves Tokens (Eager Loading)
    Job->>Job: Interpolates dynamic variables (e.g. ${title})
    Job->>Adapter: execute(configuration, token)
    Adapter->>API: HTTP POST to external API
    API-->>Adapter: HTTP 200 OK
    Adapter-->>Job: Returns Success DTO
    Job->>DB: Updates ExecutionStep to SUCCESS
```

---

## 5. Execution History Flow (Logs)
```mermaid
sequenceDiagram
    autonumber
    actor User
    participant View as executions/index.blade.php
    participant Route as routes/web.php
    participant Controller as ExecutionController
    participant DB as Database

    User->>Route: GET /executions
    Route->>Controller: index()
    Controller->>DB: SELECT * FROM automation_executions WHERE user_id = ?
    DB-->>Controller: Returns paginated executions
    Controller-->>View: Injects $executions variable
    View-->>User: Renders history table
```

---

## 6. Error Handling Flow (API Failures / DLQ)
```mermaid
sequenceDiagram
    autonumber
    participant Redis as Redis Queue
    participant Worker as Queue Worker
    participant Job as ProcessAutomationAction
    participant Adapter as ProviderAdapter
    actor API as External API (Down/Error)
    participant DB as Database (failed_jobs)

    Worker->>Redis: Pops pending Job
    Worker->>Job: handle()
    Job->>Adapter: execute()
    Adapter->>API: Attempts HTTP Request
    API-->>Adapter: Timeout / 500 Server Error
    Adapter-->>Job: Throws Exception
    Job-->>Worker: Uncaught Exception
    Worker->>Worker: Detects Job failure
    Worker->>DB: Updates ExecutionStep to FAILED
    Worker->>DB: Inserts record into 'failed_jobs' table (DLQ)
    Worker->>Worker: Continues polling next job
```

---

## 7. Dashboard Flow (Real-time Metrics)
```mermaid
sequenceDiagram
    autonumber
    actor User
    participant View as home.blade.php
    participant Route as routes/web.php
    participant Controller as HomeController
    participant DB as Database

    User->>Route: GET /home
    Route->>Controller: index()
    Controller->>DB: COUNT(automations) WHERE active = true
    Controller->>DB: COUNT(automation_executions) today
    Controller->>DB: COUNT(service_connections)
    DB-->>Controller: Returns totals
    Controller-->>View: Injects metrics variables
    View-->>User: Renders Stats Cards on screen
```
