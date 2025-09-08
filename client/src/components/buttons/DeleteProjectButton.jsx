"use client"

import styles from "./DeleteProjectButton.module.css"

export default function DeleteProjectButton({ onClick }) {
  return (
    <button className={styles.button} onClick={onClick}>
      Delete Project
    </button>
  )
}
