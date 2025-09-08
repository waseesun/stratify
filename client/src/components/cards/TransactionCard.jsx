"use client"

import { useRouter } from "next/navigation"
import styles from "./TransactionCard.module.css"

export default function TransactionCard({ transaction }) {
  const router = useRouter()

  const handleClick = () => {
    router.push(`/transactions/${transaction.id}`)
  }

  const formatAmount = (amount) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount)
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    })
  }

  return (
    <div className={styles.card} onClick={handleClick}>
      <div className={styles.header}>
        <h3 className={styles.milestoneName}>{transaction.milestone_name}</h3>
        <span className={styles.amount}>{formatAmount(transaction.amount)}</span>
      </div>

      <div className={styles.details}>
        <div className={styles.detailItem}>
          <span className={styles.label}>Project ID:</span>
          <span className={styles.value}>{transaction.project_id}</span>
        </div>

        <div className={styles.detailItem}>
          <span className={styles.label}>Release Date:</span>
          <span className={styles.value}>{formatDate(transaction.release_date)}</span>
        </div>

        <div className={styles.detailItem}>
          <span className={styles.label}>Created:</span>
          <span className={styles.value}>{formatDate(transaction.created_at)}</span>
        </div>
      </div>
    </div>
  )
}
