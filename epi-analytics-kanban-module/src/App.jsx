import React, { useState, useEffect } from 'react'
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd'
import { Plus, Trash2, Edit2, Save, X } from 'lucide-react'
import './App.css'

const INITIAL_DATA = {
  columns: {
    'pending': {
      id: 'pending',
      title: 'Pendiente',
      taskIds: []
    },
    'in-progress': {
      id: 'in-progress',
      title: 'En Progreso',
      taskIds: []
    },
    'review': {
      id: 'review',
      title: 'En Revisión',
      taskIds: []
    },
    'completed': {
      id: 'completed',
      title: 'Completado',
      taskIds: []
    }
  },
  tasks: {},
  columnOrder: ['pending', 'in-progress', 'review', 'completed']
}

function App() {
  const [data, setData] = useState(() => {
    const saved = localStorage.getItem('kanban-data')
    return saved ? JSON.parse(saved) : INITIAL_DATA
  })
  const [newTaskColumnId, setNewTaskColumnId] = useState(null)
  const [newTaskTitle, setNewTaskTitle] = useState('')
  const [newTaskDescription, setNewTaskDescription] = useState('')
  const [newTaskPriority, setNewTaskPriority] = useState('medium')
  const [editingTask, setEditingTask] = useState(null)

  useEffect(() => {
    localStorage.setItem('kanban-data', JSON.stringify(data))
  }, [data])

  const onDragEnd = (result) => {
    const { destination, source, draggableId } = result

    if (!destination) return
    if (destination.droppableId === source.droppableId && 
        destination.index === source.index) return

    const startColumn = data.columns[source.droppableId]
    const finishColumn = data.columns[destination.droppableId]

    if (startColumn === finishColumn) {
      const newTaskIds = Array.from(startColumn.taskIds)
      newTaskIds.splice(source.index, 1)
      newTaskIds.splice(destination.index, 0, draggableId)

      const newColumn = {
        ...startColumn,
        taskIds: newTaskIds
      }

      setData({
        ...data,
        columns: {
          ...data.columns,
          [newColumn.id]: newColumn
        }
      })
    } else {
      const startTaskIds = Array.from(startColumn.taskIds)
      startTaskIds.splice(source.index, 1)
      const newStart = {
        ...startColumn,
        taskIds: startTaskIds
      }

      const finishTaskIds = Array.from(finishColumn.taskIds)
      finishTaskIds.splice(destination.index, 0, draggableId)
      const newFinish = {
        ...finishColumn,
        taskIds: finishTaskIds
      }

      setData({
        ...data,
        columns: {
          ...data.columns,
          [newStart.id]: newStart,
          [newFinish.id]: newFinish
        }
      })
    }
  }

  const addTask = (columnId) => {
    if (!newTaskTitle.trim()) return

    const taskId = `task-${Date.now()}`
    const newTask = {
      id: taskId,
      title: newTaskTitle,
      description: newTaskDescription,
      priority: newTaskPriority,
      createdAt: new Date().toISOString()
    }

    const column = data.columns[columnId]
    const newTaskIds = [...column.taskIds, taskId]

    setData({
      ...data,
      tasks: {
        ...data.tasks,
        [taskId]: newTask
      },
      columns: {
        ...data.columns,
        [columnId]: {
          ...column,
          taskIds: newTaskIds
        }
      }
    })

    setNewTaskTitle('')
    setNewTaskDescription('')
    setNewTaskPriority('medium')
    setNewTaskColumnId(null)
  }

  const deleteTask = (taskId, columnId) => {
    const newTasks = { ...data.tasks }
    delete newTasks[taskId]

    const column = data.columns[columnId]
    const newTaskIds = column.taskIds.filter(id => id !== taskId)

    setData({
      ...data,
      tasks: newTasks,
      columns: {
        ...data.columns,
        [columnId]: {
          ...column,
          taskIds: newTaskIds
        }
      }
    })
  }

  const startEditTask = (task) => {
    setEditingTask({
      ...task,
      editTitle: task.title,
      editDescription: task.description,
      editPriority: task.priority
    })
  }

  const saveEditTask = () => {
    if (!editingTask.editTitle.trim()) return

    setData({
      ...data,
      tasks: {
        ...data.tasks,
        [editingTask.id]: {
          ...data.tasks[editingTask.id],
          title: editingTask.editTitle,
          description: editingTask.editDescription,
          priority: editingTask.editPriority
        }
      }
    })
    setEditingTask(null)
  }

  const getPriorityColor = (priority) => {
    const colors = {
      low: '#10b981',
      medium: '#f59e0b',
      high: '#ef4444'
    }
    return colors[priority] || colors.medium
  }

  const getPriorityLabel = (priority) => {
    const labels = {
      low: 'Baja',
      medium: 'Media',
      high: 'Alta'
    }
    return labels[priority] || 'Media'
  }

  return (
    <div className="app">
      <header className="header">
        <h1>📋 Kanban - Programador de Tareas</h1>
        <p>Organiza tu trabajo de forma visual y eficiente</p>
      </header>

      <DragDropContext onDragEnd={onDragEnd}>
        <div className="board">
          {data.columnOrder.map(columnId => {
            const column = data.columns[columnId]
            const tasks = column.taskIds.map(taskId => data.tasks[taskId])

            return (
              <div key={column.id} className="column">
                <div className="column-header">
                  <h3>{column.title}</h3>
                  <span className="task-count">{tasks.length}</span>
                </div>

                <Droppable droppableId={column.id}>
                  {(provided, snapshot) => (
                    <div
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                      className={`task-list ${snapshot.isDraggingOver ? 'dragging-over' : ''}`}
                    >
                      {tasks.map((task, index) => (
                        <Draggable key={task.id} draggableId={task.id} index={index}>
                          {(provided, snapshot) => (
                            <div
                              ref={provided.innerRef}
                              {...provided.draggableProps}
                              {...provided.dragHandleProps}
                              className={`task-card ${snapshot.isDragging ? 'dragging' : ''}`}
                            >
                              {editingTask?.id === task.id ? (
                                <div className="task-edit-form">
                                  <input
                                    type="text"
                                    value={editingTask.editTitle}
                                    onChange={(e) => setEditingTask({
                                      ...editingTask,
                                      editTitle: e.target.value
                                    })}
                                    className="edit-input"
                                    placeholder="Título de la tarea"
                                  />
                                  <textarea
                                    value={editingTask.editDescription}
                                    onChange={(e) => setEditingTask({
                                      ...editingTask,
                                      editDescription: e.target.value
                                    })}
                                    className="edit-textarea"
                                    placeholder="Descripción"
                                    rows="2"
                                  />
                                  <select
                                    value={editingTask.editPriority}
                                    onChange={(e) => setEditingTask({
                                      ...editingTask,
                                      editPriority: e.target.value
                                    })}
                                    className="edit-select"
                                  >
                                    <option value="low">Prioridad Baja</option>
                                    <option value="medium">Prioridad Media</option>
                                    <option value="high">Prioridad Alta</option>
                                  </select>
                                  <div className="edit-actions">
                                    <button onClick={saveEditTask} className="btn-save">
                                      <Save size={16} /> Guardar
                                    </button>
                                    <button onClick={() => setEditingTask(null)} className="btn-cancel">
                                      <X size={16} /> Cancelar
                                    </button>
                                  </div>
                                </div>
                              ) : (
                                <>
                                  <div className="task-header">
                                    <h4>{task.title}</h4>
                                    <span 
                                      className="priority-badge"
                                      style={{ backgroundColor: getPriorityColor(task.priority) }}
                                    >
                                      {getPriorityLabel(task.priority)}
                                    </span>
                                  </div>
                                  {task.description && (
                                    <p className="task-description">{task.description}</p>
                                  )}
                                  <div className="task-actions">
                                    <button 
                                      onClick={() => startEditTask(task)}
                                      className="btn-icon"
                                      title="Editar"
                                    >
                                      <Edit2 size={16} />
                                    </button>
                                    <button 
                                      onClick={() => deleteTask(task.id, column.id)}
                                      className="btn-icon btn-delete"
                                      title="Eliminar"
                                    >
                                      <Trash2 size={16} />
                                    </button>
                                  </div>
                                </>
                              )}
                            </div>
                          )}
                        </Draggable>
                      ))}
                      {provided.placeholder}

                      {newTaskColumnId === column.id ? (
                        <div className="new-task-form">
                          <input
                            type="text"
                            value={newTaskTitle}
                            onChange={(e) => setNewTaskTitle(e.target.value)}
                            placeholder="Título de la tarea"
                            className="new-task-input"
                            autoFocus
                          />
                          <textarea
                            value={newTaskDescription}
                            onChange={(e) => setNewTaskDescription(e.target.value)}
                            placeholder="Descripción (opcional)"
                            className="new-task-textarea"
                            rows="2"
                          />
                          <select
                            value={newTaskPriority}
                            onChange={(e) => setNewTaskPriority(e.target.value)}
                            className="new-task-select"
                          >
                            <option value="low">Prioridad Baja</option>
                            <option value="medium">Prioridad Media</option>
                            <option value="high">Prioridad Alta</option>
                          </select>
                          <div className="new-task-actions">
                            <button onClick={() => addTask(column.id)} className="btn-add">
                              Agregar Tarea
                            </button>
                            <button 
                              onClick={() => {
                                setNewTaskColumnId(null)
                                setNewTaskTitle('')
                                setNewTaskDescription('')
                                setNewTaskPriority('medium')
                              }}
                              className="btn-cancel"
                            >
                              Cancelar
                            </button>
                          </div>
                        </div>
                      ) : (
                        <button 
                          onClick={() => setNewTaskColumnId(column.id)}
                          className="btn-new-task"
                        >
                          <Plus size={18} />
                          <span>Nueva Tarea</span>
                        </button>
                      )}
                    </div>
                  )}
                </Droppable>
              </div>
            )
          })}
        </div>
      </DragDropContext>
    </div>
  )
}

export default App
