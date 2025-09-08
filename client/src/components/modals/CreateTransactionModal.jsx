"use client"

import { useState } from "react"
import TransactionForm from "@/components/forms/TransactionForm"
import styles from "./CreateTransactionModal.module.css"

export default function CreateTransactionModal({ onClose, onSuccess }) {
  const [isSubmitting, setIsSubmitting] = useState(false)

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      onClose()
    }
  }

  return (
    <div className={styles.backdrop} onClick={handleBackdropClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Create New Transaction</h2>
          <button type="button" className={styles.closeButton} onClick={onClose} disabled={isSubmitting}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <TransactionForm onSuccess={onSuccess} onSubmitting={setIsSubmitting} />
        </div>
      </div>
    </div>
  )
}
