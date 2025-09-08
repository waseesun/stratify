import { useFormStatus } from "react-dom"
import styles from "./CreateReviewSubmitButton.module.css"

export default function CreateReviewSubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.submitButton}>
      {pending ? "Creating..." : "Create Review"}
    </button>
  )
}
