import type { CaseState, CaseType, AppealState, ComplaintState } from "./enums";
// Declarative inventory only; guard descriptions must be implemented server-side.
export const caseTransitions = [
  {
    "action": "SUBMIT",
    "fromState": "DRAFT",
    "toState": "SUBMITTED",
    "permission": "case.create.self / case.create.represented",
    "guard": "Owner/active mandate; rules valid; idempotency; version; no duplicate active ticket",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "PRECHECK",
    "fromState": "SUBMITTED",
    "toState": "PRECHECKED",
    "permission": "SYSTEM",
    "guard": "Persist validation snapshot; program rules or data completeness; no eligibility decision; pre-submit failure returns 422",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "ASSIGN",
    "fromState": "PRECHECKED",
    "toState": "UNDER_REVIEW",
    "permission": "case.assign",
    "guard": "Scoped operator; active task unique; no self-review bypass",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "REQUEST_REVISION",
    "fromState": "UNDER_REVIEW",
    "toState": "REVISION_REQUIRED",
    "permission": "case.verify.assigned",
    "guard": "Assigned operator; reason, fields/evidence, deadline",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "RESUBMIT",
    "fromState": "REVISION_REQUIRED",
    "toState": "RESUBMITTED",
    "permission": "case.revise.own",
    "guard": "Owner/mandate; same ticket; new immutable revision; deadline",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "REVALIDATE",
    "fromState": "RESUBMITTED",
    "toState": "UNDER_REVIEW",
    "permission": "SYSTEM",
    "guard": "Revalidate rules and revision, restore task; invalid submit returns 422 before commit",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "RECOMMEND",
    "fromState": "UNDER_REVIEW",
    "toState": "PENDING_APPROVAL",
    "permission": "case.verify.assigned",
    "guard": "Assigned operator; evidence and reason; creates independent review task",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "RECHECK",
    "fromState": "PENDING_APPROVAL",
    "toState": "UNDER_REVIEW",
    "permission": "case.review.assigned",
    "guard": "Independent reviewer; reason and task",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "RECOMMEND_FORWARD",
    "fromState": "PENDING_APPROVAL",
    "toState": "RECOMMENDED_FOR_FORWARD",
    "permission": "case.review.assigned",
    "guard": "Independent reviewer; rule/evidence/next step; P0 result only",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "QUEUE_FORWARD",
    "fromState": "RECOMMENDED_FOR_FORWARD",
    "toState": "FORWARDING_PENDING",
    "permission": "SYSTEM",
    "guard": "P1 simulator enabled; persist outbox and correlation ID; no success claim",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "ACK_FORWARD",
    "fromState": "FORWARDING_PENDING",
    "toState": "FORWARDED_SIMULATED",
    "permission": "SYSTEM",
    "guard": "Verified simulator ACK matches case, version and correlation; synthetic only",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "FAIL_FORWARD",
    "fromState": "FORWARDING_PENDING",
    "toState": "FORWARDING_FAILED",
    "permission": "SYSTEM",
    "guard": "Timeout/failure recorded; no ACK; preserve reason",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "RETRY_FORWARD",
    "fromState": "FORWARDING_FAILED",
    "toState": "FORWARDING_PENDING",
    "permission": "SYSTEM",
    "guard": "P1 enabled; bounded retry with same logical correlation/idempotency; no duplicate",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "CLOSE_UNPROCESSABLE",
    "fromState": "PENDING_APPROVAL",
    "toState": "CLOSED_UNPROCESSABLE",
    "permission": "case.review.assigned",
    "guard": "Independent reviewer; reason, evidence, rule and appeal policy",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "NOT_SELECT",
    "fromState": "PENDING_APPROVAL",
    "toState": "NOT_SELECTED",
    "permission": "case.review.assigned",
    "guard": "Authorized program-specific rule only; reason/evidence; no official benefit determination",
    "caseTypes": [
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "CLOSE",
    "fromState": "RECOMMENDED_FOR_FORWARD",
    "toState": "CLOSED",
    "permission": "SYSTEM",
    "guard": "P0 only; internal result delivered in web inbox, appeal window expired or no appeal allowed; no active appeal/forward job",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "CLOSE",
    "fromState": "FORWARDED_SIMULATED",
    "toState": "CLOSED",
    "permission": "SYSTEM",
    "guard": "Result in inbox; appeal window expired or not eligible; no active appeal",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "CLOSE",
    "fromState": "CLOSED_UNPROCESSABLE",
    "toState": "CLOSED",
    "permission": "SYSTEM",
    "guard": "Result in inbox; appeal window expired or not eligible; no active appeal",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "CLOSE",
    "fromState": "NOT_SELECTED",
    "toState": "CLOSED",
    "permission": "SYSTEM",
    "guard": "Result in inbox; appeal window expired or not eligible; no active appeal",
    "caseTypes": [
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "APPEAL_RECHECK",
    "fromState": "CLOSED_UNPROCESSABLE",
    "toState": "UNDER_REVIEW",
    "permission": "SYSTEM",
    "guard": "Independent appeal decision RECHECK_REQUIRED or DECISION_CORRECTED; preserve prior decision; new task",
    "caseTypes": [
      "DATA_PROPOSAL",
      "PROGRAM_APPLICATION"
    ]
  },
  {
    "action": "APPEAL_RECHECK",
    "fromState": "NOT_SELECTED",
    "toState": "UNDER_REVIEW",
    "permission": "SYSTEM",
    "guard": "Independent appeal decision RECHECK_REQUIRED or DECISION_CORRECTED; preserve prior decision; new task",
    "caseTypes": [
      "PROGRAM_APPLICATION"
    ]
  }
] as const satisfies readonly {action:string;fromState:CaseState;toState:CaseState;permission:string;guard:string;caseTypes:readonly CaseType[]}[];
export const appealTransitions = [
  {
    "action": "ASSIGN_APPEAL",
    "fromState": "APPEAL_SUBMITTED",
    "toState": "APPEAL_REVIEW",
    "permission": "appeal.assign",
    "guard": "Reviewer assigned by authorized coordinator; different from all prior decision makers; no self assignment"
  },
  {
    "action": "UPHOLD",
    "fromState": "APPEAL_REVIEW",
    "toState": "UPHELD",
    "permission": "appeal.review.independent",
    "guard": "Reason and evidence, prior decision preserved"
  },
  {
    "action": "RECHECK",
    "fromState": "APPEAL_REVIEW",
    "toState": "RECHECK_REQUIRED",
    "permission": "appeal.review.independent",
    "guard": "Atomic parent APPEAL_RECHECK and new task"
  },
  {
    "action": "CORRECT",
    "fromState": "APPEAL_REVIEW",
    "toState": "DECISION_CORRECTED",
    "permission": "appeal.review.independent",
    "guard": "Correction rationale; parent APPEAL_RECHECK, no automatic new outcome"
  },
  {
    "action": "CLOSE",
    "fromState": "UPHELD",
    "toState": "APPEAL_CLOSED",
    "permission": "SYSTEM",
    "guard": "Result available in inbox"
  },
  {
    "action": "CLOSE",
    "fromState": "RECHECK_REQUIRED",
    "toState": "APPEAL_CLOSED",
    "permission": "SYSTEM",
    "guard": "Result in inbox and linked new task exists"
  },
  {
    "action": "CLOSE",
    "fromState": "DECISION_CORRECTED",
    "toState": "APPEAL_CLOSED",
    "permission": "SYSTEM",
    "guard": "Result in inbox and linked new task exists"
  }
] as const satisfies readonly {action:string;fromState:AppealState;toState:AppealState;permission:string;guard:string}[];
export const complaintTransitions = [
  {
    "action": "TRIAGE",
    "fromState": "RECEIVED",
    "toState": "TRIAGED",
    "permission": "complaint.manage.assigned",
    "guard": "Scoped triage; category and confidentiality"
  },
  {
    "action": "ASSIGN",
    "fromState": "TRIAGED",
    "toState": "ASSIGNED",
    "permission": "complaint.assign",
    "guard": "Scoped handler, no self assignment"
  },
  {
    "action": "START",
    "fromState": "ASSIGNED",
    "toState": "IN_HANDLING",
    "permission": "complaint.manage.assigned",
    "guard": "Assigned handler"
  },
  {
    "action": "RESOLVE",
    "fromState": "IN_HANDLING",
    "toState": "RESOLVED",
    "permission": "complaint.manage.assigned",
    "guard": "Outcome and reason"
  },
  {
    "action": "REFER",
    "fromState": "IN_HANDLING",
    "toState": "REFERRED",
    "permission": "complaint.manage.assigned",
    "guard": "Referral destination and reason; no actual external transmission claim"
  },
  {
    "action": "CLOSE",
    "fromState": "RESOLVED",
    "toState": "CLOSED",
    "permission": "complaint.manage.assigned",
    "guard": "Outcome communicated via inbox"
  },
  {
    "action": "CLOSE",
    "fromState": "REFERRED",
    "toState": "CLOSED",
    "permission": "complaint.manage.assigned",
    "guard": "Referral communicated via inbox"
  }
] as const satisfies readonly {action:string;fromState:ComplaintState;toState:ComplaintState;permission:string;guard:string}[];
