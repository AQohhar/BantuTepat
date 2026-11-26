// v1.2 candidate freeze; GUEST is a public access context, not a persisted role.
export const AUTH_ROLES = ["CITIZEN", "REGIONAL_PROPOSER", "VILLAGE_OPERATOR", "AGENCY_VERIFIER", "PROGRAM_MANAGER", "AUDITOR", "SUPER_ADMIN"] as const;
export type AuthRole = typeof AUTH_ROLES[number];
export const CASE_TYPES = ["DATA_PROPOSAL", "PROGRAM_APPLICATION"] as const;
export type CaseType = typeof CASE_TYPES[number];
export const CASE_STATES = ["DRAFT", "SUBMITTED", "PRECHECKED", "UNDER_REVIEW", "REVISION_REQUIRED", "RESUBMITTED", "PENDING_APPROVAL", "RECOMMENDED_FOR_FORWARD", "FORWARDING_PENDING", "FORWARDING_FAILED", "FORWARDED_SIMULATED", "CLOSED_UNPROCESSABLE", "NOT_SELECTED", "CLOSED"] as const;
export type CaseState = typeof CASE_STATES[number];
export const APPEAL_STATES = ["APPEAL_SUBMITTED", "APPEAL_REVIEW", "UPHELD", "RECHECK_REQUIRED", "DECISION_CORRECTED", "APPEAL_CLOSED"] as const;
export type AppealState = typeof APPEAL_STATES[number];
export const COMPLAINT_STATES = ["RECEIVED", "TRIAGED", "ASSIGNED", "IN_HANDLING", "RESOLVED", "REFERRED", "CLOSED"] as const;
export type ComplaintState = typeof COMPLAINT_STATES[number];
export const PERMISSIONS = ["program.read_public", "program.draft.create", "program.draft.update", "program.submit_review", "program.publish", "program.archive", "case.create.self", "case.create.represented", "case.read.own", "case.read.assigned", "case.revise.own", "case.verify.assigned", "case.review.assigned", "case.appeal.own", "appeal.review.independent", "complaint.create", "complaint.manage.assigned", "audit.read.scoped", "metrics.read.public", "metrics.read.internal", "users.manage_roles", "integration.manage", "ai.assessment.read.scoped", "case.assign", "appeal.assign", "case.attachment.write", "case.attachment.read", "complaint.read.own", "complaint.assign", "notification.read.own", "representation.manage", "territory.manage", "knowledge.approve", "knowledge.draft.create", "representation.approve"] as const;
export type Permission = typeof PERMISSIONS[number];
export type AccessContext = AuthRole | "GUEST";
