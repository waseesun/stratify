"use client"

import { useRouter } from "next/navigation"
import styles from "./ReviewCard.module.css"

export default function ReviewCard({ review, currentUserId, onUpdate, onDelete }) {
  const router = useRouter()

  const handleCardClick = () => {
    router.push(`/review/${review.id}`)
  }

  const renderStars = (rating) => {
    return Array.from({ length: 5 }, (_, index) => (
      <span key={index} className={`${styles.star} ${index < rating ? styles.filled : styles.empty}`}>
        ★
      </span>
    ))
  }

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    })
  }

  const isReviewer = currentUserId == review.reviewer_id

  return (
    <div className={styles.reviewCard} onClick={handleCardClick}>
      <div className={styles.reviewHeader}>
        <div className={styles.rating}>
          {renderStars(review.rating)}
          <span className={styles.ratingNumber}>({review.rating}/5)</span>
        </div>
        <div className={styles.date}>{formatDate(review.created_at)}</div>
      </div>

      <div className={styles.comment}>{review.comment}</div>

      {isReviewer && (
        <div className={styles.actions} onClick={(e) => e.stopPropagation()}>
          <button
            className={styles.updateButton}
            onClick={(e) => {
              e.stopPropagation()
              onUpdate(review)
            }}
          >
            Update
          </button>
          <button
            className={styles.deleteButton}
            onClick={(e) => {
              e.stopPropagation()
              onDelete(review.id)
            }}
          >
            Delete
          </button>
        </div>
      )}
    </div>
  )
}
