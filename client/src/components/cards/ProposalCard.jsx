"use client"

import { useRouter } from "next/navigation"
import styles from "./ProposalCard.module.css"

export default function ProposalCard({ proposal }) {
  const router = useRouter()

  const handleClick = () => {
    router.push(`/proposals/${proposal.id}`)
  }

  const getStatusClass = (status) => {
    switch (status) {
      case "submitted":
        return styles.statusSubmitted
      case "accepted":
        return styles.statusAccepted
      case "rejected":
        return styles.statusRejected
      default:
        return styles.statusDefault
    }
  }

  return (
    <div className={styles.card} onClick={handleClick}>
      <div className={styles.header}>
        <h3 className={styles.title}>{proposal.title}</h3>
        <span className={`${styles.status} ${getStatusClass(proposal.status)}`}>{proposal.status}</span>
      </div>

      <p className={styles.description}>
        {proposal.description?.length > 150 ? `${proposal.description.substring(0, 150)}...` : proposal.description}
      </p>

      <div className={styles.footer}>
        <div className={styles.problemId}>Problem ID: {proposal.problem_id}</div>
        <div className={styles.date}>{new Date(proposal.created_at).toLocaleDateString()}</div>
      </div>
    </div>
  )
}
