"use client"

import styles from "./UserTypeCard.module.css"

export default function UserTypeCard({ title, description, icon, onClick, isSelected }) {
  return (
    <div className={`${styles.card} ${isSelected ? styles.selected : ""}`} onClick={onClick}>
      <div className={styles.icon}>{icon}</div>
      <h3 className={styles.title}>{title}</h3>
      <p className={styles.description}>{description}</p>
    </div>
  )
}
