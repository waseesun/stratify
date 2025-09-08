"use client"

import { useEffect } from "react"
import UpdateProposalForm from "@/components/forms/UpdateProposalForm"
import styles from "./UpdateProposalModal.module.css"

export default function UpdateProposalModal({ proposal, onClose, onSuccess }) {
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === "Escape") {
        onClose()
      }
    }

    document.addEventListener("keydown", handleEscape)
    document.body.style.overflow = "hidden"

    return () => {
      document.removeEventListener("keydown", handleEscape)
      document.body.style.overflow = "unset"
    }
  }, [onClose])

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      onClose()
    }
  }

  return (
    <div className={styles.overlay} onClick={handleBackdropClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Update Proposal</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <UpdateProposalForm proposal={proposal} onSuccess={onSuccess} onCancel={onClose} />
        </div>
      </div>
    </div>
  )
}
