"use client"

import { useState } from "react"
import { deleteTransactionAction } from "@/actions/transactionActions"
import styles from "./DeleteTransactionModal.module.css"

export default function DeleteTransactionModal({ transaction, onClose, onSuccess }) {
  const [isDeleting, setIsDeleting] = useState(false)
  const [error, setError] = useState("")

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget && !isDeleting) {
      onClose()
    }
  }

  const handleDelete = async () => {
    setIsDeleting(true)
    setError("")

    try {
      const result = await deleteTransactionAction(transaction.id)

      if (result.error) {
        setError(typeof result.error === "string" ? result.error : "Failed to delete transaction")
      } else if (result.success) {
        onSuccess()
      }
    } catch (err) {
      setError("An unexpected error occurred")
    } finally {
      setIsDeleting(false)
    }
  }

  return (
    <div className={styles.backdrop} onClick={handleBackdropClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Delete Transaction</h2>
        </div>

        <div className={styles.content}>
          <p className={styles.message}>
            Are you sure you want to delete the transaction "{transaction.milestone_name}"? This action cannot be
            undone.
          </p>

          {error && <div className={styles.error}>{error}</div>}
        </div>

        <div className={styles.actions}>
          <button type="button" className={styles.cancelButton} onClick={onClose} disabled={isDeleting}>
            No, Cancel
          </button>
          <button type="button" className={styles.deleteButton} onClick={handleDelete} disabled={isDeleting}>
            {isDeleting ? "Deleting..." : "Yes, Delete"}
          </button>
        </div>
      </div>
    </div>
  )
}
