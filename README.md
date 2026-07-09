![DroneNAV](https://avatars.githubusercontent.com/u/287328252?s=400&u=42c97657ee8df9c220c0bf0d1cf7a0fe811c1fff&v=4)

---

# DroneNav Governance Platform

The DroneNav Governance Platform provides the administrative, governance, and content management capabilities of the DroneNav ecosystem. Built on the Drupal Content Management System, it delivers a configurable, secure, and extensible environment for managing spatial governance data, survey workflows, user administration, and regulatory information while remaining independent of the operational flight services.

Unlike the DroneNav operational components, which are responsible for real-time spatial data and flight operations, Drupal serves as the governance layer of the overall platform. This separation of concerns allows each component of the DroneNav architecture to evolve independently while maintaining clearly defined responsibilities.

---

# Repository Purpose

This repository contains the Drupal implementation used to support DroneNav governance and administration.

The repository includes:

* Drupal core installation
* Composer-managed contributed modules
* DroneNav custom modules
* Configuration management
* Content types and taxonomies
* Administrative Views
* Governance workflows
* User roles and permissions

Detailed implementation of the custom DroneNav modules is documented within their respective repositories and source code. This document focuses on the Drupal platform and the architectural decisions that support the DroneNav ecosystem.

---

# Overall DroneNav Architecture

DroneNav is intentionally designed as a collection of independent services, each responsible for a specific area of functionality.

| Component                      | Responsibility                                                                          |
| ------------------------------ | --------------------------------------------------------------------------------------- |
| **Drupal Governance Platform** | Administrative interface, governance workflows, content management, user administration |
| **Flask API**                  | Business logic, operational services, spatial processing, REST endpoints                |
| **PostgreSQL/PostGIS**         | Operational spatial database                                                            |
| **React Client**               | Interactive mapping and user interface                                                  |
| **NAVProxy**                   | Vehicle integration layer                                                               |
| **Documentation Repository**   | Architecture and technical documentation                                                |

This separation allows each component to evolve independently while maintaining clean interfaces between services.

---

# Why Drupal?

Drupal was selected because it provides a mature enterprise content management framework capable of supporting the governance requirements of DroneNav without requiring the development of custom administrative infrastructure.

Drupal provides:

* Enterprise content management
* Flexible content modeling
* Role-based security
* Workflow management
* Revision history
* Configuration management
* Administrative dashboards
* Extensible module architecture
* Mature REST integration
* Long-term community support

Rather than building these capabilities from scratch, DroneNav leverages Drupal's proven platform while custom modules implement DroneNav-specific governance functionality.

---

# Governance Responsibilities

The Drupal platform is responsible for administrative and governance functions including:

* Site administration
* User and role management
* Authority management
* Spatial overlay governance
* Survey management
* Survey review workflows
* Configuration management
* Administrative reporting
* Taxonomy management
* Documentation and informational content

Drupal intentionally does **not** perform operational flight processing, navigation calculations, telemetry processing, or spatial routing. Those responsibilities belong to the DroneNav operational services.

---

# Drupal Core

The DroneNav Governance Platform is built on the latest stable Drupal Core release using Composer for dependency management.

Core Drupal capabilities leveraged by DroneNav include:

* Content Types
* Taxonomies
* Views
* Content Moderation
* Workflows
* Media Library
* Configuration Management
* JSON:API
* REST Services
* Role-based Permissions
* Revision Management

These features provide a stable foundation upon which DroneNav governance capabilities are built.

---

# Contributed Modules

DroneNav intentionally limits contributed modules to those that provide clear architectural value.

## Workflow and Governance

### Content Moderation

Provides configurable governance workflows for content review and publication.

### Workflows

Defines approval processes and publication state transitions for governance activities.

### Views

Builds administrative dashboards, workbenches, reports, and management screens without unnecessary custom code.

### Views Bulk Operations

Supports efficient administration of large collections of governance records.

---

## Geographic Information Management

### Geofield

Provides native storage of geographic points, lines, and polygons used throughout the governance platform.

### Geocoder

Supports conversion between geographic coordinates and address information where appropriate.

### Geofield Map

Provides visualization of spatial data within Drupal administrative interfaces.

---

## Administrative Productivity

### Field Group

Organizes complex administrative forms into logical sections that improve usability.

### Inline Entity Form

Allows referenced entities to be created and managed directly within parent forms.

### Pathauto

Automatically generates consistent URLs throughout the governance platform.

### Token

Provides reusable tokens used by Pathauto and other automation features.

### Profile

Supports extended user profile information beyond Drupal's standard user account.

### Login Destination

Allows different classes of users to begin their work in role-specific administrative areas.

### Masquerade

Allows administrators to temporarily assume another user's identity for troubleshooting and support purposes.

---

## Collaboration

### Forum

Provides discussion capabilities for governance participants and administrators.

### Flag

Supports user-specific tracking and workflow assistance where appropriate.

---

## Integration

### JSON:API

Provides standards-based access to Drupal content for external services.

### RESTful Web Services

Supports integration between Drupal and the DroneNav operational platform.

---

## Development Utilities

### Devel

Installed only within development environments to simplify debugging and testing.

This module is not required for production operation.

---

# Custom DroneNav Modules

The DroneNav Governance Platform extends Drupal through a collection of custom modules that implement business-specific functionality.

These modules include capabilities such as:

* Survey Workbench
* Overlay synchronization
* Governance dashboards
* Administrative workflows
* Custom permissions
* Platform integration
* DroneNav business logic

Implementation details for these modules are documented within their respective repositories and source code and are intentionally not duplicated within this document.

---

# Design Principles

The Drupal implementation follows several architectural principles.

## Separation of Concerns

Drupal is responsible for governance and administration.

Operational processing is delegated to the Flask API and associated services.

## Configuration Before Code

Whenever practical, Drupal configuration is preferred over custom code. Content types, Views, permissions, workflows, and taxonomies are configured using Drupal's native capabilities.

## Minimal Customization

Custom code is developed only when Drupal core and contributed modules cannot reasonably provide the required functionality.

## API-First Integration

Drupal communicates with operational services through well-defined REST interfaces rather than direct database access.

## UUID-Based Integration

Operational objects are referenced using stable UUIDs, allowing each platform component to maintain independent persistence while preserving referential integrity.

---

# Development Environment

The DroneNav Governance Platform is developed using a modern Composer-based Drupal workflow.

Development tools include:

* Composer
* Drush
* Git
* PostgreSQL
* PHP
* Configuration Management
* Local development environments

Configuration should be exported and version-controlled to ensure consistent deployments across development, testing, and production environments.

---

# Relationship to Other Repositories

This repository represents only the governance component of the DroneNav platform.

Additional repositories include:

* **DroneNav API** — Operational REST services and business logic
* **DroneNav React Client** — Interactive mapping application
* **DroneNav Database** — PostgreSQL/PostGIS schema
* **NAVProxy** — Vehicle integration services
* **DroneNav Documentation** — Architecture and design documentation

Together these repositories form the complete DroneNav platform.

---

# License

DroneNav is released as open-source software under the GNU Affero General Public License (AGPL v3).

See the LICENSE file included with this repository for complete licensing information.
