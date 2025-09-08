"use client"
import { useFormStatus } from "react-dom"
import { useState } from "react"
import { useRouter } from "next/navigation"
import { logoutAction } from "@/actions/authActions"
import styles from "./Buttons.module.css"
import CreateProposalModal from "@/components/modals/CreateProposalModal"
import UpdateProposalModal from "../modals/UpdateProposalModal"
import DeleteProposalModal from "../modals/DeleteProposalModal"
export function LoginButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.loginButton}>
      {pending ? "Logging in..." : "Login"}
    </button>
  )
}

export function LogoutButton() {
  const { pending } = useFormStatus()

  return (
    <form action={logoutAction}>
      <button type="submit" className={styles.logoutButton} disabled={pending}>
        {pending ? "Logging Out..." : "Logout"}
      </button>
    </form>
  )
}

export function RegisterButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.registerButton}>
      {pending ? "Registering..." : "Register"}
    </button>
  )
}

export function UserTypeButton({ children, onClick, userType, disabled }) {
  return (
    <button className={styles.button} onClick={() => onClick(userType)} disabled={disabled}>
      {children}
    </button>
  )
}


export function UpdateButton({ children, type = "submit" }) {
  const { pending } = useFormStatus()

  return (
    <button type={type} className={styles.updateButton} disabled={pending}>
      {pending ? "Updating..." : children}
    </button>
  )
}


export function CreateProblemButton({ onClick }) {
  return (
    <button className={styles.CreatePbutton} onClick={onClick}>
      Create Problem
    </button>
  )
}

export function DeleteProblemButton({ onClick }) {
  return (
    <button className={styles.DeletePbutton} onClick={onClick}>
      Delete Problem
    </button>
  )
}

export function SubmitButton({ children = "Submit" }) {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={`${styles.Submitbutton} ${pending ? styles.Submitloading : ""}`}>
      {pending ? "Submitting..." : children}
    </button>
  )
}

export function UpdateProblemButton({ onClick }) {
  return (
    <button className={styles.UpdatePbutton} onClick={onClick}>
      Update Problem
    </button>
  )
}

export function CreateProposalButton({ onSuccess }) {
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleOpenModal = () => {
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
  }

  const handleSuccess = () => {
    setIsModalOpen(false)
    if (onSuccess) {
      onSuccess()
    }
  }

  return (
    <>
      <button className={styles.CProposalbutton} onClick={handleOpenModal}>
        Create Proposal
      </button>

      {isModalOpen && <CreateProposalModal onClose={handleCloseModal} onSuccess={handleSuccess} />}
    </>
  )
}

export function DeleteProposalButton({ proposalId }) {
  const [isModalOpen, setIsModalOpen] = useState(false)
  const router = useRouter()

  const handleOpenModal = () => {
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
  }

  const handleSuccess = () => {
    setIsModalOpen(false)
    router.push("/proposals")
  }

  return (
    <>
      <button className={styles.DProposalbutton} onClick={handleOpenModal}>
        Delete
      </button>

      {isModalOpen && (
        <DeleteProposalModal proposalId={proposalId} onClose={handleCloseModal} onSuccess={handleSuccess} />
      )}
    </>
  )
}

export function UpdateProposalButton({ proposal, onSuccess }) {
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleOpenModal = () => {
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
  }

  const handleSuccess = () => {
    setIsModalOpen(false)
    if (onSuccess) {
      onSuccess()
    }
  }

  return (
    <>
      <button className={styles.UProposalbutton} onClick={handleOpenModal}>
        Update
      </button>

      {isModalOpen && <UpdateProposalModal proposal={proposal} onClose={handleCloseModal} onSuccess={handleSuccess} />}
    </>
  )
}

export function CreateCategoryButton({ onClick, disabled = false }) {
  return (
    <button type="button" onClick={onClick} disabled={disabled} className={styles.createCategoryButton}>
      <svg
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        className={styles.icon}
      >
        <path d="M12 5v14M5 12h14" />
      </svg>
      Create Category
    </button>
  )
}

export function RemoveButton({ onClick, disabled = false }) {
  const { pending } = useFormStatus()

  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled || pending}
      className={styles.removeButton}
      aria-label="Remove item"
    >
      {pending ? (
        <span className={styles.removeButton.spinner}></span>
      ) : (
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      )}
    </button>
  )
}