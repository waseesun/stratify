"use client"
import ReviewForm from "../forms/ReviewForm"
import styles from "./CreateReviewModal.module.css"

export default function CreateReviewModal({ isOpen, onClose, revieweeId, onSuccess }) {
  if (!isOpen) return null

  return (
    <div className={styles.modalOverlay} onClick={onClose}>
      <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
        <div className={styles.modalHeader}>
          <h2 className={styles.modalTitle}>Create Review</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>
        <ReviewForm revieweeId={revieweeId} onSuccess={onSuccess} onCancel={onClose} />
      </div>
    </div>
  )
}
