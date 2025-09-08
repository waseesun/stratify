"use client"

import { useRouter } from "next/navigation"
import styles from "./ProblemCard.module.css"

export default function ProblemCard({ problem }) {
  const router = useRouter()

  const handleClick = () => {
    router.push(`/problems/${problem.id}`)
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

  return (
    <div className={styles.card} onClick={handleClick}>
      <div className={styles.header}>
        <h3 className={styles.title}>{problem.title}</h3>
        <span className={`${styles.status} ${getStatusClass(problem.status)}`}>{problem.status}</span>
      </div>

      <p className={styles.description}>{problem.description}</p>

      <div className={styles.details}>
        <div className={styles.budget}>
          <span className={styles.label}>Budget:</span>
          <span className={styles.value}>{formatBudget(problem.budget)}</span>
        </div>

        <div className={styles.timeline}>
          <span className={styles.label}>Timeline:</span>
          <span className={styles.value}>{getTimelineText(problem.timeline_value, problem.timeline_unit)}</span>
        </div>
      </div>
    </div>
  )
}
