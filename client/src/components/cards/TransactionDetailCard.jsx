"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import DeleteTransactionButton from "@/components/buttons/DeleteTransactionButton"
import DeleteTransactionModal from "@/components/modals/DeleteTransactionModal"
import styles from "./TransactionDetailCard.module.css"

export default function TransactionDetailCard({ transaction }) {
  const router = useRouter()
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false)

  const formatAmount = (amount) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount)
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    })
  }

  const handleDeleteSuccess = () => {
    setIsDeleteModalOpen(false)
    router.push("/transactions")
  }

  return (
    <div className={styles.card}>
      <div className={styles.header}>
        <div>
          <h1 className={styles.title}>{transaction.milestone_name}</h1>
          <p className={styles.amount}>{formatAmount(transaction.amount)}</p>
        </div>
        <DeleteTransactionButton onClick={() => setIsDeleteModalOpen(true)} />
      </div>

      <div className={styles.content}>
        <div className={styles.section}>
          <h2 className={styles.sectionTitle}>Transaction Details</h2>
          <div className={styles.detailsGrid}>
            <div className={styles.detailItem}>
              <span className={styles.label}>Transaction ID:</span>
              <span className={styles.value}>{transaction.id}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Project ID:</span>
              <span className={styles.value}>{transaction.project_id}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Provider ID:</span>
              <span className={styles.value}>{transaction.provider_id}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Company ID:</span>
              <span className={styles.value}>{transaction.company_id}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Release Date:</span>
              <span className={styles.value}>{formatDate(transaction.release_date)}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Created At:</span>
              <span className={styles.value}>{formatDate(transaction.created_at)}</span>
            </div>

            <div className={styles.detailItem}>
              <span className={styles.label}>Updated At:</span>
              <span className={styles.value}>{formatDate(transaction.updated_at)}</span>
            </div>
          </div>
        </div>
      </div>

      {isDeleteModalOpen && (
        <DeleteTransactionModal
          transaction={transaction}
          onClose={() => setIsDeleteModalOpen(false)}
          onSuccess={handleDeleteSuccess}
        />
      )}
    </div>
  )
}
