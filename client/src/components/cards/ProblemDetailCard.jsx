"use client"

import { useState } from "react"
import AcceptProposalButton from "@/components/buttons/AcceptProposalButton"
import AcceptProposalModal from "@/components/modals/AcceptProposalModal"
import styles from "./ProblemDetailCard.module.css"
import Link from "next/link"

export default function ProblemDetailCard({ problem }) {
  const [showAcceptModal, setShowAcceptModal] = useState(false)
  const [selectedProposal, setSelectedProposal] = useState(null)

  const handleAcceptProposal = (proposal) => {
    setSelectedProposal(proposal)
    setShowAcceptModal(true)
  }

  const isProposalAccepted = (proposalId) => {
    if (problem.proposals && problem.proposals.length > 0) {
      return problem.proposals.some((proposal) => proposal.id === proposalId && (proposal.status === "accepted" || proposal.status === "rejected"))
    }
    return false
  }

  const getStatusClass = (status) => {
    switch (status) {
      case "open":
        return styles.statusOpen
      case "sold":
        return styles.statusSold
      case "cancelled":
        return styles.statusCancelled
      default:
        return styles.statusDefault
    }
  }

  const formatBudget = (budget) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(budget)
  }

  const getTimelineText = (value, unit) => {
    const unitText = unit === 0 ? "day" : unit === 1 ? "week" : "month"
    return `${value} ${unitText}${value !== 1 ? "s" : ""}`
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
    })
  }

  return (
    <>
      <div className={styles.card}>
        <div className={styles.header}>
          <h1 className={styles.title}>{problem.title}</h1>
          <span className={`${styles.status} ${getStatusClass(problem.status)}`}>{problem.status}</span>
        </div>

        <div className={styles.section}>
          <h3 className={styles.sectionTitle}>Description</h3>
          <p className={styles.description}>{problem.description}</p>
        </div>

        <div className={styles.detailsGrid}>
          <div className={styles.detail}>
            <span className={styles.label}>Budget</span>
            <span className={styles.value}>{formatBudget(problem.budget)}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Timeline</span>
            <span className={styles.value}>{getTimelineText(problem.timeline_value, problem.timeline_unit)}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Company</span>
            <Link href={`/profile/${problem.company_id}`} className={styles.value}>
              <span className={styles.value}>{problem.company_name || `ID: ${problem.company_id}`}</span>
            </Link>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Category ID</span>
            <span className={styles.value}>{problem.category_id}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Created</span>
            <span className={styles.value}>{formatDate(problem.created_at)}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Updated</span>
            <span className={styles.value}>{formatDate(problem.updated_at)}</span>
          </div>
        </div>

        {problem.skillsets && problem.skillsets.length > 0 && (
          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Required Skills</h3>
            <div className={styles.skills}>
              {problem.skillsets.map((skillset) => (
                <span key={skillset.id} className={styles.skill}>
                  {skillset.skill}
                </span>
              ))}
            </div>
          </div>
        )}

        {problem.proposals && problem.proposals.length > 0 && (
          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Proposals ({problem.proposals.length})</h3>
            <div className={styles.proposals}>
              {problem.proposals.map((proposal) => (
                <div key={proposal.id} className={styles.proposal}>
                  <div className={styles.proposalHeader}>
                    <h4 className={styles.proposalTitle}>{proposal.title}</h4>
                    <div className={styles.proposalActions}>
                      <span
                        className={`${styles.proposalStatus} ${styles[`status${proposal.status.charAt(0).toUpperCase() + proposal.status.slice(1)}`]}`}
                      >
                        {proposal.status}
                      </span>
                      {!isProposalAccepted(proposal.id) && (
                        <AcceptProposalButton onClick={() => handleAcceptProposal(proposal)} />
                      )}
                    </div>
                  </div>
                  <p className={styles.proposalProvider}>by {proposal.provider.username}</p>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>

      {showAcceptModal && selectedProposal && (
        <AcceptProposalModal
          problem={problem}
          proposal={selectedProposal}
          onClose={() => {
            setShowAcceptModal(false)
            setSelectedProposal(null)
          }}
        />
      )}
    </>
  )
}
