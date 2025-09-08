"use client"

import { useRouter } from "next/navigation"
import styles from "./ProjectCard.module.css"

export default function ProjectCard({ project }) {
  const router = useRouter()

  const handleClick = () => {
    router.push(`/projects/${project.id}`)
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString()
  }

  const getStatusColor = (status) => {
    switch (status) {
      case "in_progress":
        return styles.statusInProgress
      case "completed":
        return styles.statusCompleted
      case "cancelled":
        return styles.statusCancelled
      default:
        return styles.statusDefault
    }
  }

  return (
    <div className={styles.card} onClick={handleClick}>
      <div className={styles.header}>
        <h3 className={styles.title}>{project.problem_title}</h3>
        <span className={`${styles.status} ${getStatusColor(project.status)}`}>{project.status.replace("_", " ")}</span>
      </div>

      <div className={styles.content}>
        <p className={styles.proposalTitle}>
          <strong>Proposal:</strong> {project.proposal_title}
        </p>

        <div className={styles.details}>
          <div className={styles.detail}>
            <span className={styles.label}>Problem ID:</span>
            <span className={styles.value}>{project.problem_id}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Fee:</span>
            <span className={styles.value}>${project.fee?.toLocaleString()}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>Start Date:</span>
            <span className={styles.value}>{formatDate(project.start_date)}</span>
          </div>

          <div className={styles.detail}>
            <span className={styles.label}>End Date:</span>
            <span className={styles.value}>{formatDate(project.end_date)}</span>
          </div>
        </div>
      </div>
    </div>
  )
}
