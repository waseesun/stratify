"use client"
import styles from "./CreateReviewButton.module.css"

export default function CreateReviewButton({ onClick }) {
  return (
    <button className={styles.createButton} onClick={onClick}>
      Create Review
    </button>
  )
}
